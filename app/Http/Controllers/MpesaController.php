<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class MpesaController extends Controller
{
    private function getAccessToken()
    {
        try {
            $url = (config('services.mpesa.env') === 'sandbox') 
                ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' 
                : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

            $response = Http::withBasicAuth(
                config('services.mpesa.consumer_key'),
                config('services.mpesa.consumer_secret')
            )->timeout(30)->get($url);

            if ($response->failed()) {
                Log::error('M-Pesa token error: ' . $response->body());
                throw new \Exception('Failed to generate access token');
            }

            return $response->json()['access_token'];
        } catch (\Exception $e) {
            Log::error('Access Token Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function stkPush(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|regex:/^254[0-9]{9}$/',
                'invoice_id' => 'required|exists:invoices,id'
            ]);

            $phone = $request->input('phone');
            $invoiceId = $request->input('invoice_id');
            
            $invoice = Invoice::findOrFail($invoiceId);
            
            $amount = (int) ceil($invoice->amount);
            if ($amount < 1) {
                throw new \Exception('Amount must be at least 1 shilling');
            }

            $transactionRef = 'TX-' . date('Ymd') . '-' . strtoupper(uniqid());
            $timestamp = now()->format('YmdHis');
            $password = base64_encode(config('services.mpesa.shortcode') . config('services.mpesa.passkey') . $timestamp);

            $url = (config('services.mpesa.env') === 'sandbox')
                ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
                : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

            $response = Http::withToken($this->getAccessToken())
                ->timeout(30)
                ->post($url, [
                    "BusinessShortCode" => config('services.mpesa.shortcode'),
                    "Password" => $password,
                    "Timestamp" => $timestamp,
                    "TransactionType" => "CustomerPayBillOnline",
                    "Amount" => $amount,
                    "PartyA" => $phone,
                    "PartyB" => config('services.mpesa.shortcode'),
                    "PhoneNumber" => $phone,
                    "CallBackURL" => config('services.mpesa.callback_url'),
                    "AccountReference" => "INV-" . $invoiceId,
                    "TransactionDesc" => "Payment for Invoice #" . $invoiceId
                ]);

            if ($response->failed()) {
                throw new \Exception('Payment initiation failed: ' . ($response->json()['errorMessage'] ?? 'Unknown error'));
            }

            $data = $response->json();
            
            if (isset($data['CheckoutRequestID'])) {
                $invoice->update([
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'transaction_ref' => $transactionRef
                ]);
            }

            Log::info('STK Push Success', [
                'invoice_id' => $invoiceId,
                'amount' => $amount,
                'phone' => $phone,
                'transaction_ref' => $transactionRef,
                'response' => $data
            ]);

            return back()->with('success', 'Payment request sent. Please check your phone.');

        } catch (\Exception $e) {
            Log::error('STK Push Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('M-Pesa Callback Received', $data);

            if (!isset($data['Body']['stkCallback'])) {
                throw new \Exception('Invalid callback data structure');
            }

            $callback = $data['Body']['stkCallback'];
            $resultCode = $callback['ResultCode'];
            $resultDesc = $callback['ResultDesc'];
            $checkoutRequestId = $callback['CheckoutRequestID'];

            if ($resultCode == 0) {
                $metadata = collect($callback['CallbackMetadata']['Item']);

                $payment = Payment::create([
                    'invoice_id' => null,
                    'amount' => $metadata->firstWhere('Name', 'Amount')['Value'] ?? null,
                    'method' => 'mpesa',
                    'mpesa_receipt' => $metadata->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null,
                    'payer_phone' => $metadata->firstWhere('Name', 'PhoneNumber')['Value'] ?? null,
                    'paid_at' => Carbon::now()
                ]);

                $invoice = Invoice::where('checkout_request_id', $checkoutRequestId)->first();

                if ($invoice) {
                    // Update payment with invoice ID
                    $payment->update(['invoice_id' => $invoice->id]);

                    // Calculate total amount paid
                    $totalPaid = $invoice->payments()->sum('amount');

                    // Check if payment exceeds invoice amount
                    if ($totalPaid > $invoice->amount) {
                        Log::warning('Payment exceeds invoice amount', [
                            'invoice_id' => $invoice->id,
                            'invoice_amount' => $invoice->amount,
                            'total_paid' => $totalPaid
                        ]);
                    }

                    // Update invoice status to paid if fully paid
                    if ($totalPaid >= $invoice->amount && $invoice->status !== 'paid') {
                        $invoice->update(['status' => 'paid']);
                        Log::info('Invoice marked as paid', [
                            'invoice_id' => $invoice->id,
                            'amount' => $invoice->amount,
                            'total_paid' => $totalPaid
                        ]);
                    }

                    Log::info('Payment Success', [
                        'invoice_id' => $invoice->id,
                        'amount' => $payment->amount,
                        'receipt' => $payment->mpesa_receipt
                    ]);
                }
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);

        } catch (\Exception $e) {
            Log::error('Callback Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Failed']);
        }
    }

    public function queryStatus(Invoice $invoice)
    {
        try {
            if (!$invoice->checkout_request_id) {
                throw new \Exception('No payment request found');
            }

            $timestamp = now()->format('YmdHis');
            $password = base64_encode(
                config('services.mpesa.shortcode') . 
                config('services.mpesa.passkey') . 
                $timestamp
            );

            $response = Http::withToken($this->getAccessToken())
                ->post('https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query', [
                    "BusinessShortCode" => config('services.mpesa.shortcode'),
                    "Password" => $password,
                    "Timestamp" => $timestamp,
                    "CheckoutRequestID" => $invoice->checkout_request_id
                ]);

            if ($response->failed()) {
                throw new \Exception('Status query failed');
            }

            $result = $response->json();
            
            Log::info('Payment Status', [
                'invoice_id' => $invoice->id,
                'result' => $result
            ]);

            return back()->with('info', 'Status: ' . ($result['ResultDesc'] ?? 'Unknown'));

        } catch (\Exception $e) {
            Log::error('Status Query Failed', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoice->id
            ]);
            return back()->with('error', 'Query failed: ' . $e->getMessage());
        }
    }

    public function demoPay(Invoice $invoice)
    {
        if (config('services.mpesa.env') !== 'sandbox') {
            return back()->with('error', 'Demo payments only available in sandbox');
        }

        try {
            $amount = (int) ceil($invoice->amount);
            
            $response = Http::withToken($this->getAccessToken())
                ->post('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate', [
                    "ShortCode" => config('services.mpesa.shortcode'),
                    "CommandID" => "CustomerPayBillOnline",
                    "Amount" => $amount,
                    "Msisdn" => "254708374149",
                    "BillRefNumber" => "INV-" . $invoice->id
                ]);

            if ($response->failed()) {
                throw new \Exception('Demo payment failed: ' . $response->body());
            }

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => 'mpesa',
                'mpesa_receipt' => 'DEMO-' . strtoupper(uniqid()),
                'payer_phone' => '254708374149',
                'paid_at' => Carbon::now()
            ]);

            // Calculate total amount paid
            $totalPaid = $invoice->payments()->sum('amount');

            // Check if payment exceeds invoice amount
            if ($totalPaid > $invoice->amount) {
                Log::warning('Demo payment exceeds invoice amount', [
                    'invoice_id' => $invoice->id,
                    'invoice_amount' => $invoice->amount,
                    'total_paid' => $totalPaid
                ]);
            }

            // Update invoice status to paid if fully paid
            if ($totalPaid >= $invoice->amount && $invoice->status !== 'paid') {
                $invoice->update(['status' => 'paid']);
                Log::info('Invoice marked as paid from demo payment', [
                    'invoice_id' => $invoice->id,
                    'amount' => $invoice->amount,
                    'total_paid' => $totalPaid
                ]);
            }

            Log::info('Demo Payment', [
                'invoice_id' => $invoice->id,
                'response' => $response->json()
            ]);

            return back()->with('success', 'Demo payment sent and recorded');

        } catch (\Exception $e) {
            Log::error('Demo Payment Failed', [
                'message' => $e->getMessage(),
                'invoice_id' => $invoice->id
            ]);
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}

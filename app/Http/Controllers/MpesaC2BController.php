<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\Payment;

class MpesaC2BController extends Controller
{
    private function accessToken()
    {
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $res = Http::withBasicAuth(
            config('services.mpesa.consumer_key'),
            config('services.mpesa.consumer_secret')
        )->get($url);

        if ($res->failed()) {
            Log::error('❌ Access token error', ['body' => $res->body()]);
            abort(500, 'Could not get M-Pesa access token');
        }

        return $res->json()['access_token'] ?? null;
    }

    /** STEP A: Register URLs (run once when your public URL changes) */
    public function registerC2B()
    {
        $token = $this->accessToken();

        $payload = [
            "ShortCode"      => (int) config('services.mpesa.shortcode'),
            "ResponseType"   => "Completed",
            "ConfirmationURL"=> config('services.mpesa.c2b_confirm_url'),
            "ValidationURL"  => config('services.mpesa.c2b_validate_url'),
        ];

        $res = Http::withToken($token)
            ->post('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl', $payload);

        Log::info('📌 Register C2B URLs', ['req' => $payload, 'res' => $res->json()]);
        return back()->with('success', 'C2B URLs registered (Sandbox).');
    }

    /** STEP B: Simulate a customer payment (for demo button) */
    public function simulateDemo(Invoice $invoice)
    {
        $token = $this->accessToken();

        $amount = (int) round($invoice->amount); // Ensure amount is a whole number

        $payload = [
    "ShortCode"     => 600000,                        // Sandbox C2B shortcode
    "CommandID"     => "CustomerPayBillOnline",
    "Amount"        => max(1, (int) ceil($invoice->amount)), 
    "Msisdn"        => '254708374149',               // Sandbox test number
    "BillRefNumber" => "INV-" . $invoice->id,
];


        $res = Http::withToken($token)
            ->post('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate', $payload);

        Log::info('📤 Demo C2B Simulate Sent', ['req' => $payload, 'res' => $res->json()]);
        return back()->with('success', 'Demo payment sent. Invoice will auto-update when callback arrives.');
    }

    /** STEP C: Validation URL (Daraja calls this before confirming) */
    public function validateC2B(Request $request)
    {
        Log::info('🧪 C2B Validate', $request->all());
        // You can perform checks (amount limits, format, etc.)
        return response()->json([
            "ResultCode" => 0,
            "ResultDesc" => "Accepted"
        ]);
    }

    /** STEP D: Confirmation URL (Daraja sends the actual payment data here) */
    
public function confirmC2B(Request $request)
{
    $data = $request->all();
    Log::info('✅ C2B Confirm', $data);

    $amount       = $data['TransAmount']     ?? null;
    $mpesaReceipt = $data['TransID']         ?? null;
    $phone        = $data['MSISDN']          ?? null;
    $billRef      = $data['BillRefNumber']   ?? null;
    $transTime    = $data['TransTime']       ?? null;

    $payment = Payment::create([
        'invoice_id'    => null,
        'amount'        => $amount,
        'method'        => 'mpesa',
        'mpesa_receipt' => $mpesaReceipt,
        'payer_phone'   => $phone,
        'paid_at'       => now(),
    ]);

    if ($billRef && str_starts_with($billRef, 'INV-')) {
        $invoiceId = (int) str_replace('INV-', '', $billRef);
        $invoice   = Invoice::find($invoiceId);

        if ($invoice) {
            $payment->update(['invoice_id' => $invoice->id]);

            $totalPaid = $invoice->payments()->sum('amount');
            if ($totalPaid >= $invoice->amount) {
                // Update status to 'paid' if it was 'pending'
                if ($invoice->status === 'pending') {
                    $invoice->update(['status' => 'paid']);
                }
            }

            Log::info("💾 Linked payment to invoice #{$invoice->id} | TotalPaid={$totalPaid}");
        } else {
            Log::warning("⚠️ Invoice not found for BillRef: {$billRef}");
        }
    } else {
        Log::warning("⚠️ BillRef missing or invalid", ['BillRefNumber' => $billRef]);
    }

    return response('OK', 200);
}
}

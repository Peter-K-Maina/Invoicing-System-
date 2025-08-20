<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class MpesaService
{
    private function getAccessToken()
    {
        $url = (env('MPESA_ENV') === 'sandbox') 
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' 
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth(
            env('MPESA_CONSUMER_KEY'),
            env('MPESA_CONSUMER_SECRET')
        )->timeout(30)->get($url);

        if ($response->failed()) {
            Log::error('M-Pesa token error: ' . $response->body());
            throw new \Exception('Failed to generate access token from M-Pesa');
        }

        return $response->json()['access_token'];
    }

    public function stkPush($phone, $amount, $invoiceId)
    {
        $timestamp = date('YmdHis');
        $password  = base64_encode(env('MPESA_SHORTCODE') . env('MPESA_PASSKEY') . $timestamp);

        $url = (env('MPESA_ENV') === 'sandbox')
            ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $response = Http::withToken($this->getAccessToken())
            ->timeout(30)
            ->post($url, [
                "BusinessShortCode" => env('MPESA_SHORTCODE'),
                "Password"          => $password,
                "Timestamp"         => $timestamp,
                "TransactionType"   => "CustomerPayBillOnline",
                "Amount"            => $amount,
                "PartyA"            => $phone,
                "PartyB"            => env('MPESA_SHORTCODE'),
                "PhoneNumber"       => $phone,
                "CallBackURL"       => config('services.mpesa.callback_url'),
                "AccountReference"  => "INV-" . $invoiceId,
                "TransactionDesc"   => "Payment for Invoice #" . $invoiceId,
            ]);

        if ($response->failed()) {
            Log::error('M-Pesa STK Push error: ' . $response->body());
            throw new \Exception('STK Push request failed');
        }

        return $response->json();
    }

    public function handleCallback($response)
    {
        Log::info('M-Pesa Callback:', $response);

        if (isset($response['Body']['stkCallback']['ResultCode']) 
            && $response['Body']['stkCallback']['ResultCode'] == 0) {

            $metadata = collect($response['Body']['stkCallback']['CallbackMetadata']['Item']);

            $amount       = optional($metadata->where('Name', 'Amount')->first())['Value'] ?? null;
            $mpesaReceipt = optional($metadata->where('Name', 'MpesaReceiptNumber')->first())['Value'] ?? null;
            $accountRef   = optional($metadata->where('Name', 'AccountReference')->first())['Value'] ?? null;

            if ($accountRef && str_starts_with($accountRef, 'INV-')) {
                $invoiceId = intval(str_replace('INV-', '', $accountRef));

                Invoice::where('id', $invoiceId)->update([
                    'status'        => 'paid',
                    'mpesa_receipt' => $mpesaReceipt,
                ]);
            }
        }

        return [
            "ResultCode" => 0,
            "ResultDesc" => "Callback received successfully",
        ];
    }
}

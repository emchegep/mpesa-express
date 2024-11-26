<?php

namespace Emchegep\MpesaExpress;

use Carbon\Carbon;
use Emchegep\MpesaExpress\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class MpesaExpress
{
    private string $consumerKey;

    private string $consumerSecret;

    private string $businessShortCode;

    private string $passkey;

    private string $timestamp;

    private string $password;

    private string $environment;

    public function __construct()
    {
        $this->consumerKey = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->environment = config_path('mpesa.environment');

        $this->businessShortCode = config('mpesa.business_short_code');
        $this->passkey = config('mpesa.pass_key');
        $this->timestamp = Carbon::now()->format('YmdHis');
        $this->password = base64_encode($this->businessShortCode.$this->passkey.$this->timestamp);
    }

    public function initiatePayment(string $phoneNumber, int $amount): void
    {
        $token = $this->getToken();

        $url = match ($this->environment) {
            'sandbox' => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            'production' => 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
            default => throw new Exception('Invalid M-PESA environment value'),
        };

        $payload = [
            'BusinessShortCode' => $this->businessShortCode,
            'Password' => $this->password,
            'Timestamp' => $this->timestamp,
            'TransactionType' => config('mpesa.transaction_type'),
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $this->businessShortCode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => config('mpesa.callback_url'),
            'AccountReference' => config('mpesa.account_reference'),
            'TransactionDesc' => config('mpesa.transaction_desc'),
        ];

        try {
            $response = Http::withToken($token)->post($url, $payload);
            $result = json_decode($response);
            $this->savePaymentDetails($result, $payload);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            abort(500, 'Unable to initiate payment');
        }
    }

    private function savePaymentDetails($paymentDetails, $payload): void
    {
        $responseCode = $paymentDetails->ResponseCode;

        if ($responseCode != 0) {
            $this->handleRequestError($responseCode);
        }

        $data = [
            'merchant_request_id' => $paymentDetails->MerchantRequestID,
            'checkout_request_id' => $paymentDetails->CheckoutRequestID,
            'phone_number' => $payload['PhoneNumber'],
            'account_reference' => $payload['AccountReference'],
            'transaction_desc' => $payload['TransactionDesc'],
            'amount' => $payload['Amount'],
            'status' => 'REQUESTED',
        ];

        Payment::create($data);
    }

    public function updatePaymentDetails($data): Payment
    {
        $resultCode = $data->Body->stkCallback->ResultCode;

        $payment = Payment::where('checkout_request_id', $data->Body->stkCallback->CheckoutRequestID)->firstOrFail();

        if ($resultCode == 0) {
            $payment->status = 'PAID';
            $payment->result_desc = $data->Body->stkCallback->ResultDesc;
            $payment->mpesa_receipt_number = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            $payment->transaction_date = $data->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $payment->save();

        } else {
            $payment->status = 'FAILED';
            $payment->result_desc = $data->Body->stkCallback->ResultDesc;
            $payment->save();
        }

        return $payment;
    }

    /**
     * @return mixed|void
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     *
     * get M-Pesa Access token
     */
    private function getToken()
    {
        $url = match ($this->environment) {
            'sandbox' => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            'production' => 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
            default => throw new Exception('Invalid M-PESA environment value'),
        };

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)->get($url);

            return $response['access_token'];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            abort(500, 'Unable to get access token');
        }
    }

    private function handleRequestError($errorCode)
    {
        $errorMessage = match ($errorCode) {
            1037 => 'DS timeout user cannot be reached or No response from the user',
            1025,9999 => 'An error occurred while sending a push request',
            1032 => 'The request was canceled by the user',
            1 => 'The balance is insufficient for the transaction',
            2001 => 'The initiator information is invalid',
            1019 => 'Transaction has expired',
            1001 => 'Unable to lock subscriber, a transaction is already in process for the current subscriber',
            default => throw new Exception("Unknown error response code $errorCode"),
        };

        abort(500, $errorMessage);
    }
}

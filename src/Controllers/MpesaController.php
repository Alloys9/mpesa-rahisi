<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Stkrequest;
use App\Models\C2brequest;
use App\Models\Mpesarequest;
use Symfony\Component\Mailer\Envelope;
use Throwable;

class MpesaController extends Controller
{
    public function token()
    {
        try {
            $consumerKey = env('MPESA_CONSUMER_KEY');
            $consumerSecret = env('MPESA_CONSUMER_SECRET');
            $url = env('MPESA_TOKEN_URL');

            $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
            return $response['access_token'];
        } catch (Throwable $e) {
            // Handle the exception or log it
            return response()->json(['error' => 'Failed to obtain token: ' . $e->getMessage()], 500);
        }
    }

    public function initiateStkPush(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'phone' => 'required|regex:/^254\d{9}$/',
        ]);
        try {
            $accessToken = $this->token();
            $url = env('MPESA_INITIATE_URL');
            $PassKey = env('SAFARICOM_PASSKEY');
            $BusinessShortCode = env('MPESA_BUSINESS_SHORTCODE');
            $Timestamp = Carbon::now()->format('YmdHis');
            $password = base64_encode($BusinessShortCode . $PassKey . $Timestamp);
            $TransactionType = 'CustomerPayBillOnline';
            $Amount = $request->input('amount');
            $PartyA = $request->input('phone');
            $PartyB = $BusinessShortCode;
            $PhoneNumber = $PartyA;
            $CallbackUrl = env('MPESA_CALLBACK_URL');
            $AccountReference = 'Payment';
            $TransactionDesc = 'payment for goods';

            $response = Http::withToken($accessToken)->post($url, [
                'BusinessShortCode' => $BusinessShortCode,
                'Password' => $password,
                'Timestamp' => $Timestamp,
                'TransactionType' => $TransactionType,
                'Amount' => $Amount,
                'PartyA' => $PartyA,
                'PartyB' => $PartyB,
                'PhoneNumber' => $PhoneNumber,
                'CallBackURL' => $CallbackUrl,
                'AccountReference' => $AccountReference,
                'TransactionDesc' => $TransactionDesc
            ]);

            $res = json_decode($response);

            $ResponseCode = $res->ResponseCode;
            if ($ResponseCode == 0) {
                $MerchantRequestID = $res->MerchantRequestID;
                $CheckoutRequestID = $res->CheckoutRequestID;
                $CustomerMessage = $res->CustomerMessage;

                // Save to database
                $payment = new Mpesarequest;
                $payment->phone = $PhoneNumber;
                $payment->amount = $Amount;
                $payment->reference = $AccountReference;
                $payment->description = $TransactionDesc;
                $payment->MerchantRequestID = $MerchantRequestID;
                $payment->CheckoutRequestID = $CheckoutRequestID;
                $payment->status = 'Pending';
                $payment->save();

                return $CustomerMessage;
            }
        } catch (Throwable $e) {
            // Handle the exception or log it
            return response()->json(['error' => 'Failed to initiate STK push: ' . $e->getMessage()], 500);
        }
    }

    public function stkCallback()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('stk.txt', $data);

            $response = json_decode($data);

            $ResultCode = $response->Body->stkCallback->ResultCode;

            if ($ResultCode == 0) {
                $MerchantRequestID = $response->Body->stkCallback->MerchantRequestID;
                $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;
                $ResultDesc = $response->Body->stkCallback->ResultDesc;
                $Amount = $response->Body->stkCallback->CallbackMetadata->Item[0]->Value;
                $MpesaReceiptNumber = $response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
                $TransactionDate = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;
                $PhoneNumber = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;

                $payment = Mpesarequest::where('CheckoutRequestID', $CheckoutRequestID)->firstOrFail();
                $payment->status = 'Paid';
                $payment->TransactionDate = $TransactionDate;
                $payment->MpesaReceiptNumber = $MpesaReceiptNumber;
                $payment->ResultDesc = $ResultDesc;
                $payment->save();
            } else {
                // Payment Failed
                $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;
                $ResultDesc = $response->Body->stkCallback->ResultDesc;
                $payment = Mpesarequest::where('CheckoutRequestID', $CheckoutRequestID)->firstOrFail();

                $payment->ResultDesc = $ResultDesc;
                $payment->status = 'Failed';
                $payment->save();
            }
        } catch (Throwable $e) {
            Storage::disk('local')->put('stkCallbackError.txt', $e->getMessage());
        }
    }

    //GETTING THE STATUS OF THE TRANSACTION
    public function stkQuery($CheckoutRequestID)
    {
        try {
            $accessToken = $this->token();
            $BusinessShortCode = env('MPESA_BUSINESS_SHORTCODE');
            $PassKey = env('SAFARICOM_PASSKEY');
            $url = env('MPESA_STK_QUERY_URL');
            $Timestamp = Carbon::now()->format('YmdHis');
            $Password = base64_encode($BusinessShortCode . $PassKey . $Timestamp);

            $response = Http::withToken($accessToken)->post($url, [
                'BusinessShortCode' => $BusinessShortCode,
                'Timestamp' => $Timestamp,
                'Password' => $Password,
                'CheckoutRequestID' => $CheckoutRequestID
            ]);

            return $response;
        } catch (Throwable $e) {
            return response()->json(['error' => 'Failed to query STK: ' . $e->getMessage()], 500);
        }
    }

//C2B API SETUP
    public function registerUrl()
    {
        try {
            $accessToken = $this->token();
            $url = env('MPESA_REGISTER_URL');
            $ShortCode = env('MPESA_SHORTCODE');
            $ResponseType = 'Completed';
            $ConfirmationURL = env('MPESA_CONFIRMATION_URL');
            $ValidationURL = env('MPESA_VALIDATION_URL');

            $response = Http::withToken($accessToken)->post($url, [
                'ShortCode' => $ShortCode,
                'ResponseType' => $ResponseType,
                'ConfirmationURL' => $ConfirmationURL,
                'ValidationURL' => $ValidationURL
            ]);

            return $response;
        } catch (Throwable $e) {
            return response()->json(['error' => 'Failed to register URL: ' . $e->getMessage()], 500);
        }
    }

    public function Simulate()
    {
        try {
            $accessToken = $this->token();
            $url = env('MPESA_SIMULATE_URL');
            $ShortCode = 600997;
            $CommandID = 'CustomerPayBillOnline';
            $Amount = 1;
            $Msisdn = 254708374149;
            $BillRefNumber = '00000';

            $response = Http::withToken($accessToken)->post($url, [
                'ShortCode' => $ShortCode,
                'CommandID' => $CommandID,
                'Amount' => $Amount,
                'Msisdn' => $Msisdn,
                'BillRefNumber' => $BillRefNumber
            ]);

            return $response;
        } catch (Throwable $e) {
            return response()->json(['error' => 'Failed to simulate transaction: ' . $e->getMessage()], 500);
        }
    }

    public function Validation()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('validation.txt', $data);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 500);
        }
    }

    public function Confirmation()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('confirmation.txt', $data);

            // Save data to DB
            $response = json_decode($data);
            $TransactionType = $response->TransactionType;
            $TransID = $response->TransID;
            $TransTime = $response->TransTime;
            $TransAmount = $response->TransAmount;
            $BusinessShortCode = $response->BusinessShortCode;
            $BillRefNumber = $response->BillRefNumber;
            $InvoiceNumber = $response->InvoiceNumber;
            $OrgAccountBalance = $response->OrgAccountBalance;
            $ThirdPartyTransID = $response->ThirdPartyTransID;
            $MSISDN = $response->MSISDN;
            $FirstName = $response->FirstName;
            $MiddleName = $response->MiddleName;
            $LastName = $response->LastName;

            $c2b = new C2brequest;
            $c2b->TransactionType = $TransactionType;
            $c2b->TransID = $TransID;
            $c2b->TransTime = $TransTime;
            $c2b->TransAmount = $TransAmount;
            $c2b->BusinessShortCode = $BusinessShortCode;
            $c2b->BillRefNumber = $BillRefNumber;
            $c2b->InvoiceNumber = $InvoiceNumber;
            $c2b->OrgAccountBalance = $OrgAccountBalance;
            $c2b->ThirdPartyTransID = $ThirdPartyTransID;
            $c2b->MSISDN = $MSISDN;
            $c2b->FirstName = $FirstName;
            $c2b->MiddleName = $MiddleName;
            $c2b->LastName = $LastName;
            $c2b->save();

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Failed to process confirmation: ' . $e->getMessage()], 500);
        }
    }

    public function qrcode()
    {
        try {
            $consumerKey = \config('safaricom.consumer_key');
            $consumerSecret = \config('safaricom.consumer_secret');

            $authUrl = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

            $request = Http::withBasicAuth($consumerKey, $consumerSecret)->get($authUrl);

            $accessToken = $request['access_token'];

            $MerchantName = 'ESKULI REVISION';
            $RefNo = 'gggsgsgg';
            $Amount = 1;
            $TrxCode = 'PB'; // BG-buy goods till, WA-mpesa agent, SM-send money, SB-send to business
            $CPI = 572555;

            $url = 'https://api.safaricom.co.ke/mpesa/qrcode/v1/generate';

            $response = Http::withToken($accessToken)->post($url, [
                'MerchantName' => $MerchantName,
                'RefNo' => $RefNo,
                'Amount' => $Amount,
                'TrxCode' => $TrxCode,
                'CPI' => $CPI
            ]);

            $data = $response['QRCode'];

            return view('welcome')->with('qrcode', $data);
        } catch (Throwable $e) {
            // Handle the exception or log it
            return response()->json(['error' => 'Failed to generate QR code: ' . $e->getMessage()], 500);
        }
    }

    public function b2c()
    {
        try {
            $accessToken = $this->token();
            $InitiatorName = 'testapi';
            $InitiatorPassword = 'Safaricom123!';
            $path = Storage::disk('local')->get('SandboxCertificate.cer');
            $pk = openssl_pkey_get_public($path);

            openssl_public_encrypt(
                $InitiatorPassword,
                $encrypted,
                $pk,
                $padding = OPENSSL_PKCS1_PADDING
            );

            $SecurityCredential = base64_encode($encrypted);
            $CommandID = 'SalaryPayment'; // BusinessPayment PromotionPayment
            $Amount = 3000;
            $PartyA = 600998;
            $PartyB = 254708374149;
            $Remarks = 'remarks';
            $QueueTimeOutURL = env('MPESA_TIMEOUT_URL');
            $ResultURL = env('MPESA_RESULT_URL');
            $Occassion = 'occassion';
            $url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';

            $response = Http::withToken($accessToken)->post($url, [
                'InitiatorName' => $InitiatorName,
                'SecurityCredential' => $SecurityCredential,
                'CommandID' => $CommandID,
                'Amount' => $Amount,
                'PartyA' => $PartyA,
                'PartyB' => $PartyB,
                'Remarks' => $Remarks,
                'QueueTimeOutURL' => $QueueTimeOutURL,
                'ResultURL' => $ResultURL,
                'Occassion' => $Occassion
            ]);

            return $response;
        } catch (Throwable $e) {
            // Handle the exception or log it
            return response()->json(['error' => 'Failed to process B2C transaction: ' . $e->getMessage()], 500);
        }
    }

    public function b2cResult()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('b2cresponse.txt', $data);
        } catch (Throwable $e) {
            // Handle the exception or log it
            Storage::disk('local')->put('b2cResultError.txt', $e->getMessage());
        }
    }

    public function b2cTimeout()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('b2ctimeout.txt', $data);
        } catch (Throwable $e) {
            // Handle the exception or log it
            Storage::disk('local')->put('b2cTimeoutError.txt', $e->getMessage());
        }
    }

    public function Reversal()
    {
        try {
            $accessToken = $this->token();
            $InitiatorPassword = 'Safaricom123!';
            $path = Storage::disk('local')->get('SandboxCertificate.cer');
            $pk = openssl_pkey_get_public($path);

            openssl_public_encrypt(
                $InitiatorPassword,
                $encrypted,
                $pk,
                $padding = OPENSSL_PKCS1_PADDING
            );

            $SecurityCredential = base64_encode($encrypted);
            $CommandID = 'TransactionReversal';
            $TransactionID = 'RAF31LULEV';
            $TransactionAmount = 3000;
            $ReceiverParty = 600998;
            $ReceiverIdentifierType = 11;
            $ResultURL = env('MPESA_REVERSAL_RESULT');
            $QueueTimeOutURL = env('MPESA_REVERSAL_TIMEOUT');
            $Remarks = 'remarks';
            $Occassion = 'occassion';
            $Initiator = 'testapi';

            $url = 'https://sandbox.safaricom.co.ke/mpesa/reversal/v1/request';

            $response = Http::withToken($accessToken)->post($url, [
                'Initiator' => $Initiator,
                'SecurityCredential' => $SecurityCredential,
                'CommandID' => $CommandID,
                'TransactionID' => $TransactionID,
                'Amount' => $TransactionAmount,
                'ReceiverParty' => $ReceiverParty,
                'ReceiverIdentifierType' => $ReceiverIdentifierType,
                'ResultURL' => $ResultURL,
                'QueueTimeOutURL' => $QueueTimeOutURL,
                'Remarks' => $Remarks,
                'Occassion' => $Occassion
            ]);

            return $response;
        } catch (Throwable $e) {
            // Handle the exception or log it
            return response()->json(['error' => 'Failed to process reversal: ' . $e->getMessage()], 500);
        }
    }

    public function reversalResult()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('reversalResult.txt', $data);
        } catch (Throwable $e) {
            // Handle the exception or log it
            Storage::disk('local')->put('reversalResultError.txt', $e->getMessage());
        }
    }

    public function reversalTimeout()
    {
        try {
            $data = file_get_contents('php://input');
            Storage::disk('local')->put('reversalTimeout.txt', $data);
        } catch (Throwable $e) {
            // Handle the exception or log it
            Storage::disk('local')->put('reversalTimeoutError.txt', $e->getMessage());
        }
    }
}

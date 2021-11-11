<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentToken;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;

class MyFatoorahController extends Controller
{


    public $apiURL;
    public $apiKey;

    public function __construct()
    {
        $this->apiURL=env('PAYMENT_GETWAY_BASE_URL');
        $this->apiKey=Setting::find(1)->payment_token;
    }



    public function index($invoice_value,$user_name,$user_phone,$user_email,$order_number){

        //Fill POST fields array
        $postFields = $this->postFields($invoice_value,$user_name,$user_phone,$user_email,$order_number);

        //Call endpoint
        $data = $this-> sendPayment($this->apiURL, $this->apiKey, $postFields);


        // return if there is error
        if($data == 'error')
            return $data;

        // //You can save payment data in database as per your needs
        // $invoiceId   = $data->InvoiceId;
        // $paymentLink = $data->InvoiceURL;

        return $data;
    }





    /* ------------------------ Functions --------------------------------------- */
    /*
    * Send Payment Endpoint Function
    */

    private function sendPayment($apiURL, $apiKey, $postFields) {

        $json = $this->callAPI("$apiURL/v2/SendPayment", $apiKey, $postFields);
        if($json == 'error')
            return $json;
        return $json->Data;
    }

    //------------------------------------------------------------------------------
    /*
    * Call API Endpoint Function
    */

    private function callAPI($endpointURL, $apiKey, $postFields = [], $requestType = 'POST') {

        $curl = curl_init($endpointURL);
        curl_setopt_array($curl, array(
            CURLOPT_CUSTOMREQUEST  => $requestType,
            CURLOPT_POSTFIELDS     => json_encode($postFields),
            CURLOPT_HTTPHEADER     => array("Authorization: Bearer $apiKey", 'Content-Type: application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ));

        $response = curl_exec($curl);
        $curlErr  = curl_error($curl);

        curl_close($curl);


        $error = $this-> handleError($response);
        if ($error || $curlErr) {
            return 'error';
        }

        return json_decode($response);
    }

    //------------------------------------------------------------------------------
    /*
    * Handle Endpoint Errors Function
    */

    private function handleError($response) {

        $json = json_decode($response);
        if (isset($json->IsSuccess) && $json->IsSuccess == true) {
            return null;
        }

        //Check for the errors
        if (isset($json->ValidationErrors) || isset($json->FieldsErrors)) {
            $errorsObj = isset($json->ValidationErrors) ? $json->ValidationErrors : $json->FieldsErrors;
            $blogDatas = array_column($errorsObj, 'Error', 'Name');

            $error = implode(', ', array_map(function ($k, $v) {
                        return "$k: $v";
                    }, array_keys($blogDatas), array_values($blogDatas)));
        } else if (isset($json->Data->ErrorMessage)) {
            $error = $json->Data->ErrorMessage;
        }

        if (empty($error)) {
            $error = (isset($json->Message)) ? $json->Message : (!empty($response) ? $response : 'API key or API URL is not correct');
        }

        return $error;
    }





    /* short cuts*/

    private function customerAddress($block=null,$street=null,$building_no=null,$address=null,$address_instruction=null){
         //Fill customer address array
        return array(
            'Block'               => $block, //optional
            'Street'              => $street, //optional
            'HouseBuildingNo'     => $building_no, //optional
            'Address'             => $address, //optional
            'AddressInstructions' => $address_instruction, //optional
        );
    }

    private function invoiceItems($items){
        //Fill invoice item array

        foreach($items as $item){
            $invoiceItems[] = [
                'ItemName'  => $item->name, //ISBAN, or SKU
                'Quantity'  => $item->quantity, //Item's quantity
                'UnitPrice' => $item->price, //Price per item
                ];
        }
        return $invoiceItems;
    }


    private function postFields($invoice_value,$user_name,$user_phone,$user_email,$order_number){
        return [
            //Fill required data
            'NotificationOption' => 'Lnk', //'SMS', 'EML', or 'ALL'
            'InvoiceValue'       => $invoice_value,
            'CustomerName'       => $user_name,
                //Fill optional data
                'DisplayCurrencyIso' => 'KWD',
                // 'MobileCountryCode'  => '+965',
                // 'CustomerMobile'     => $user_phone,
                // 'CustomerEmail'      => $user_email,
                'CallBackUrl'        => route('payment_callback'),
                'ErrorUrl'           => route('payment_error'), //or 'https://example.com/error.php'
                'Language'           => app()->getLocale(), //or 'ar'
                'CustomerReference'  => $order_number,
                //'CustomerCivilId'    => 'CivilId',
                //'UserDefinedField'   => 'This could be string, number, or array',
                //'ExpiryDate'         => '', //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
                //'SourceInfo'         => 'Pure PHP', //For example: (Laravel/Yii API Ver2.0 integration)
                //'CustomerAddress'    => $this->customerAddress($block,$street,$building_no,$address,$address_instruction),
                //'InvoiceItems'       => $this->invoiceItems($items),
        ];
    }

    public function error(Request $request){
        if(isset($request['paymentId'])){
            $postFields=[
                'Key' => $request['paymentId'],
                'KeyType' => 'PaymentId',
            ];
            $data = $this->callAPI("$this->apiURL/v2/GetPaymentStatus", $this->apiKey,$postFields );
            if(isset($data->Data->CustomerReference) && collect($data->Data->InvoiceTransactions)->last()->TransactionStatus == "Failed"){
                $order=Order::findOrFail($data->Data->CustomerReference);
                if($order->transaction && $order->payment_status == 'failed'){
                    $order->transaction()->update(['payment_id' => $request['paymentId']]);
                }else{
                    foreach($order->sizes()->get() as $size){
                        $quantity=$order->sizes->where('id',$size->id)->pluck('pivot.quantity')->first();
                        $size->update(['stock' => $size->stock+$quantity]);
                    }

                    Transaction::create(['payment_id' => $request['paymentId'],'order_id' => $data->Data->CustomerReference]);
                    $order->update(['payment_status' => 'failed']);
                    $order->save();
                }

                return view('front.payment_failed');
            }
        }

    }
    public function callback(Request $request){
        if(isset($request['paymentId'])){
            $postFields=[
                'Key' => $request['paymentId'],
                'KeyType' => 'PaymentId',
            ];
            $data = $this->callAPI("$this->apiURL/v2/GetPaymentStatus", $this->apiKey,$postFields );
            if(isset($data->Data->CustomerReference) && collect($data->Data->InvoiceTransactions)->last()->TransactionStatus == "Succss"){
                $order=Order::findOrFail($data->Data->CustomerReference);
                if(!$order->transaction){
                    Transaction::create(['payment_id' => $request['paymentId'],'order_id' => $data->Data->CustomerReference]);
                    $order->update(['payment_status' => 'paid']);
                    $order->save();
                }elseif($order->transaction && $order->payment_status == 'failed'){
                    foreach($order->sizes()->get() as $size){
                        $quantity=$order->sizes->where('id',$size->id)->pluck('pivot.quantity')->first();
                        $size->update(['stock' => $size->stock-$quantity]);
                    }
                    $order->update(['payment_status' => 'paid']);
                    $order->save();
                }
                return view('front.payment_success');
            }
        }
    }
}

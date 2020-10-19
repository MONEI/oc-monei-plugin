<?php

use MONEI\MONEI\Models\Order;
use MONEI\MONEI\Classes\Monei;
use Illuminate\Http\Request;
use MONEI\MONEI\Classes\Helper;
use MONEI\MONEI\Classes\Contracts\GatewayInterface;

Route::post(Monei::URL_CALLBACK, function (Request $request) {
//    $sSignature = $request->header('MONEI-Signature');
//
//    $arSignature = explode(',', $sSignature);
//
//    $arPairs = [];
//    foreach ($arSignature as $item) {
//        $arKeyVal = explode('=', $item);
//        $arPairs[$arKeyVal[0]] = $arKeyVal[1];
//    }
//
//    $sMessage = $this->signatureConcatenate($arArgs);
//
//    hash_hmac('sha256', $sMessage, $this->arConfig['password']);

    //\Log::debug(['Monei::URL_CALLBACK, post()', Monei::URL_CALLBACK, post()]);
    $result = Event::fire(Helper::EVENT_URL_CALLBACK);

    if (isset($result[0]) && $result[0] instanceof GatewayInterface) {
        $obGateway = $result[0];
    } else {
        $obGateway = Monei::instance();
    }

    $obOrder = Order::where('order_id_full', post('orderId'))->first();

    $arPayment = $obGateway->getPayment(post('id'));

    $obOrder->updateFromPaymentResponse($arPayment);

    //\Log::debug(['updateFromPaymentResponse']);
    return response('OK');
});

Route::get('/monei/test'/*MONEIGateway::URL_CALLBACK*/, function () {

    $sURL = "https://api.monei.net/v1/payments/ce89d850b5503fbd01e3d7ef08fdedce"; // The POST URL

    $aHTTP['http']['method']  = 'GET';
    $aHTTP['http']['header'] = "Authorization: pk_test_1d54bde5bcf461aeee5df38fe1eb2f97";

    $context = stream_context_create($aHTTP);
    $contents = file_get_contents($sURL, false, $context);

    echo $contents;
    return;


    $data = array(
        //"accountId" => "425ab338-faa9-426d-b86b-16b7799d6058",
    );

    $post_data = json_encode($data);

    // Prepare new cURL resource
    $crl = curl_init('https://api.monei.net/v1/payments/ce89d850b5503fbd01e3d7ef08fdedce');
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($crl, CURLINFO_HEADER_OUT, true);
    curl_setopt($crl, CURLOPT_POST, false);
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);

    // Set HTTP Header for POST request
    curl_setopt($crl, CURLOPT_HTTPHEADER, array(
        //'Content-Type: application/json',
        //'Content-Length: ' . strlen($post_data),
        'Authorization: pk_test_1d54bde5bcf461aeee5df38fe1eb2f97',
    ));

    // Submit the POST request
    $result = curl_exec($crl);
    trace_log('$result ' . $result);
    // handle curl error
    if ($result === false) {
        // throw new Exception('Curl error: ' . curl_error($crl));
        //print_r('Curl error: ' . curl_error($crl));
        $result_noti = 0; die();
    } else {

        $result_noti = 1; die();
    }
    // Close cURL session handle
    curl_close($crl);

    return;

    $result = Event::fire(Helper::EVENT_URL_CALLBACK);

    if (isset($result[0]) && $result[0] instanceof GatewayInterface) {
        $obGateway = $result[0];
    } else {
        $obGateway = new MONEIGateway();
    }

    if (!$obGateway->checkIpnRequestIsValid()) {
        return;
    }

    return $obGateway->processSuccessRequest(post());
});

//Route::post(MONEIGateway::URL_CALLBACK_REFUND, function () {
//    trace_log('URL_CALLBACK_REFUND', post());
//    $result = Event::fire(Helper::EVENT_URL_CALLBACK_REFUND);
//
//    if (isset($result[0]) && $result[0] instanceof GatewayInterface) {
//        $obGateway = $result[0];
//    } else {
//        $obGateway = new MONEIGateway();
//    }
//
//    if (!$obGateway->checkSignature()) {
//        return;
//    }
//
//    return $obGateway->processRefundRequest(post());
//});
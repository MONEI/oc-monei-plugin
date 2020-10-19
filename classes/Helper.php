<?php namespace MONEI\MONEI\Classes;


use GuzzleHttp\RequestOptions;

class Helper
{
    const EVENT_PAYMENT_AFTER_PAY = 'monei.payment.after_pay';
    const EVENT_URL_CALLBACK = 'monei.url.callback';
    const EVENT_URL_CALLBACK_REFUND = 'monei.url.callback_refund';

    public static function log($arData = [])
    {
        self::logLine('logging array:');

        foreach ($arData as $sKey=>$sVal) {
            self::logLine($sVal, $sKey);
        }
    }

    public static function logLine($sVal, $sKey = '')
    {
        $sLine = 'monei: ';
        if ($sKey) {
            $sLine .= $sKey . ': ' . $sVal;
        } else {
            $sLine .= $sVal;
        }

        trace_log($sLine);
    }

    public static function sendRequest($sUrl, $arData = [])
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post($sUrl, [
            RequestOptions::JSON => $arData,
        ]);

// url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();

// or when your server returns json
// $content = json_decode($response->getBody(), true);

        if ($statusCode == 200) {
            return $content;
        } else {
            throw new \Exception("error request to MONEI");
        }
    }
}
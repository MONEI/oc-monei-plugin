<?php namespace MONEI\MONEI\Classes;


class Helper
{
    const EVENT_URL_CALLBACK = 'monei.url.callback';

//    public static function getOrderIdByFullId($sOrderIdFull)
//    {
//        $iOrderId  = (int) substr($sOrderIdFull, 3); // cojo los 9 digitos del final.
//
//        return $iOrderId;
//    }

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
}
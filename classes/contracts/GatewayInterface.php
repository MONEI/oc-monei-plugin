<?php namespace MONEI\MONEI\Classes\Contracts;


interface GatewayInterface
{
    public function checkIpnRequestIsValid();

    public function getArgs($obOrder);

    public function processSuccessRequest($arData);
}
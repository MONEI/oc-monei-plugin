<?php

use MONEI\MONEI\Classes\MONEIGateway;
use MONEI\MONEI\Models\Settings;
use MONEI\MONEI\Classes\Helper;
use MONEI\MONEI\Classes\GatewayInterface;

Route::post(MONEIGateway::URL_CALLBACK, function () {
    $result = Event::fire(Helper::EVENT_URL_CALLBACK);

    if ($result[0] instanceof GatewayInterface) {
        $obGateway = $result[0];
    } else {
        $obGateway = new MONEIGateway(Settings::instance()->configArray());
    }

    if (!$obGateway->checkIpnRequestIsValid()) {
        return;
    }

    return $obGateway->processSuccessRequest(post());
});
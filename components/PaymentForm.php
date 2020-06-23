<?php namespace MONEI\MONEI\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use MONEI\MONEI\Classes\MONEIGateway;
use MONEI\MONEI\Models\Settings;

class PaymentForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'monei.monei::lang.component.payment_form.details.name',
            'description' => 'monei.monei::lang.component.payment_form.details.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'url_cancel' => [
                'title'       => 'monei.monei::lang.component.payment_form.url_cancel.label',
                'description' => 'monei.monei::lang.component.payment_form.url_cancel.description',
                'default'     => '',
                'type'        => 'dropdown',
                'options'     => Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName'),
            ],
            'url_complete' => [
                'title'       => 'monei.monei::lang.component.payment_form.url_complete.label',
                'description' => 'monei.monei::lang.component.payment_form.url_complete.description',
                'default'     => '',
                'type'        => 'dropdown',
                'options'     => Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName'),
            ],
        ];
    }

    public function getUrl()
    {
        return MONEIGateway::URL_CHECKOUT;
    }

    public function getHiddenInputs($obOrder)
    {
        $arParams = [
            'url_cancel' => $this->controller->pageUrl($this->property('url_cancel')),
            'url_complete' => $this->controller->pageUrl($this->property('url_complete')),
        ];

        $obGateway = new MONEIGateway(Settings::instance()->configArray(), $arParams);

        return $obGateway->getFormInputs($obOrder);
    }
}
<?php namespace MONEI\MONEI\Components;

use Event;
use Exception;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use http\Client;
use MONEI\MONEI\Classes\Helper;
use MONEI\MONEI\Classes\Monei;
use MONEI\MONEI\Classes\MONEIGateway;
use MONEI\MONEI\Models\Order;
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
            'url_callback' => [
                'title'       => 'monei.monei::lang.component.payment_form.url_callback.label',
                'description' => 'monei.monei::lang.component.payment_form.url_callback.description',
                'default'     => '',
                'type'        => 'dropdown',
                'options'     => Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName'),
            ],
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

    public function onSendRequest()
    {
        try {
            if (!post('amount')) {
                throw new Exception('Amount is required');
            }

            $obOrder = null;

            if (post('order_id')) {
                $obOrder = Order::find(post('order_id'));
            }

            if (!$obOrder) {
                $obOrder = new Order();
            }

            $obOrder->total = post('amount') * 100;

            if (post('name')) {
                $obOrder->name = post('name');
            }

            if (post('email')) {
                $obOrder->email = post('email');
            }

            if (post('phone')) {
                $obOrder->phone = post('phone');
            }

            if (post('description')) {
                $obOrder->description = post('description');
            }

            if (post('currency')) {
                $obOrder->currency = post('currency');
            }

            $arParams = [
                'cancelUrl' => $this->controller->pageUrl($this->property('url_cancel')),
                'completeUrl' => $this->controller->pageUrl($this->property('url_complete')),
            ];

            $obOrder->generateOrderIdFull();
            $obOrder->save();

            $obPayment = Monei::instance()->pay($obOrder, $arParams);

            if ($obPayment) {
                Event::fire(Helper::EVENT_PAYMENT_AFTER_PAY);

                if ($obPayment->getNextAction()) {
                    return redirect($obPayment->getNextAction()->getRedirectUrl());
                }
            } else {
                return response(["status" => "ServiceUnavailableError"]);
            }
        } catch (\Exception $e) {
            return response(["status" => "Error"]);
        }
    }
}
<?php namespace MONEI\MONEI\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Cms;
use MONEI\MONEI\Classes\Monei;
use MONEI\MONEI\Models\Order;
use Illuminate\Support\Facades\Redirect;
use MONEI\MONEI\Classes\MONEIGateway;
use MONEI\MONEI\Models\Settings;

class Orders extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'monei.orders'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('MONEI.MONEI', 'main-menu-monei');
    }

    public function onRefund()
    {
        $obOrder = Order::find(post('order_id'));

        if (!is_numeric(post('amount')) || post('amount') <= 0) {
            throw new \ValidationException(['delete' => 'Wrong amount format']);
        }
        $iAmount = post('amount') * 100;

        if (post('amount') > 0 && $obOrder->canRefund($iAmount)) {
            return $this->askForRefund($obOrder, $iAmount);
        } else {
            $sRefundErrorHtml = $this->makePartial('$/monei/monei/views/refund_error.htm');

            return [
                '#popup-html' => $sRefundErrorHtml,
            ];
        }
    }

    public function onRefundOpen()
    {
        return $this->makePartial(plugins_path('monei/monei/views/refund_form.htm'), ['order_id' => post('order_id')]);
    }

    public function askForRefund($obOrder, $iAmount)
    {
        $arParams = [
            'amount' => $iAmount,
        ];

        $obPayment = Monei::instance()->refund($obOrder, $arParams);
\Log::debug(['$obPayment', $obPayment]);
        $this->asExtension('FormController')->initForm($obOrder);

        $refundsHtml = $this->asExtension('FormController')->formRender();

        return [
            '#form-update' => $refundsHtml,
        ];
    }
}

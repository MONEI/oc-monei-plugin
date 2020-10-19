<?php namespace MONEI\MONEI\Components;

use Cms\Classes\ComponentBase;
use MONEI\Monei\Models\Order;
use MONEI\Monei\Classes\Monei;

class SuccessPage extends ComponentBase
{
    /*
     * MONEI\Monei\Models\Order
     */
    public $obOrder;

    /*
     * array
     */
    public $arPayment;

    /*
     * Component details
     */
    public function componentDetails()
    {
        return [
            'name'        => 'monei.monei::lang.component.success_page.details.name',
            'description' => 'monei.monei::lang.component.success_page.details.description',
        ];
    }

    /*
     * Initiate order object and get payment data.
     */
    public function onRun()
    {
        $this->obOrder = Order::where('order_id_full', (int)get('orderId'))->first();

        if ($this->obOrder) {
            $this->arPayment = Monei::instance()->getPayment(get('id'));

            $this->obOrder->updateFromPaymentResponse($this->arPayment);
        }
    }

    /*
     * Get Order object.
     */
    public function getOrder()
    {
        return $this->obOrder;
    }

    /*
     * Get payment array.
     */
    public function getPayment()
    {
        return $this->arPayment;
    }
}
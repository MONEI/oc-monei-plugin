<?php namespace MONEI\MONEI\Models;

use Model;
use MONEI\MONEI\Classes\Contracts\OrderInterface;
use October\Rain\Argon\Argon;

/**
 * Order Model
 */
class Order extends Model
{
    const STATUS_ON_HOLD = 'on-hold';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SUCCEEDED = 'SUCCEEDED';
    const STATUS_REFUNDED = 'REFUNDED';
    const STATUS_PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'monei_monei_orders';

    protected $dates = ['payment_date'];

    public $jsonable = ['refunds'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['transaction_id','first_name','last_name','total','payment_status','payment_date'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function generateOrderIdFull()
    {
        if (!$this->order_id_full) {
            $this->order_id_full = 10000000000;

            $obOrderPrevious = Order::orderByDesc('order_id_full')->orderByDesc('created_at')->first();
            if ($obOrderPrevious) {
                if ((int)$obOrderPrevious->order_id_full > 0) {
                    $this->order_id_full = (int)$obOrderPrevious->order_id_full + 1;
                }
            }
        }
    }

    public function updateStatus($sStatus, $sMsg = '')
    {
        $this->payment_status = $sStatus;
        $this->payment_status_msg = $sMsg;
    }

    public function getStatusesRefundableAttribute()
    {
        $arStatusesRefundable = [
            self::STATUS_SUCCEEDED,
            self::STATUS_PARTIALLY_REFUNDED,
        ];

        return $arStatusesRefundable;
    }

    public function getIsRefundableAttribute()
    {
        return in_array($this->payment_status, $this->statusesRefundable) && $this->RefundAvailable > 0;
    }

    public function getRefundedTotalAttribute()
    {
        $iTotal = 0;

        if (is_array($this->refunds))
        foreach ($this->refunds as $refund) {
            $iTotal += $refund['amount'];
        }

        return $iTotal;
    }

    public function getRefundAvailableAttribute()
    {
        return $this->total - $this->refunded_total;
    }

    public function canRefund($iAmount)
    {
        return $iAmount <= $this->refund_available;
    }

    public function getRefunds()
    {
        $arRefunds = [];

        if (is_array($this->refunds)) {
            $arRefunds = $this->refunds;
        }

        $arRefundsSort = [];
        foreach ($arRefunds as $arRefund) {
            $arRefundsSort[$arRefund['date']] = $arRefund;
        }

        krsort($arRefundsSort);

        return $arRefundsSort;
    }

    public function getTotalFullAttribute()
    {
        return $this->attributes['total'] / 100 . ' ' . $this->attributes['currency'];
    }

    public function updateFromPaymentResponse($arPayment)
    {
        $this->updateStatus($arPayment['status'], $arPayment['statusMessage']);
        $this->payment_date = Argon::now();
        $this->transaction_id = $arPayment['id'];
        $this->currency = $arPayment['currency'];
        $this->account_id = $arPayment['accountId'];

        if (isset($arPayment['customer']['name'])) {
            $this->name = $arPayment['customer']['name'];
        }
        if (isset($arPayment['customer']['email'])) {
            $this->email = $arPayment['customer']['email'];
        }
        if (isset($arPayment['customer']['phone'])) {
            $this->phone = $arPayment['customer']['phone'];
        }

        $this->save();
    }
}

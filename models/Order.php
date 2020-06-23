<?php namespace MONEI\MONEI\Models;

use Model;
use MONEI\MONEI\Classes\Contracts\OrderInterface;

/**
 * Order Model
 */
class Order extends Model implements OrderInterface
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'monei_monei_orders';

    protected $dates = ['payment_date'];

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

    public function setOrderIFull()
    {
        if (!$this->order_id_full) {
            $sTransactionId     = str_pad($this->id, 12, '0', STR_PAD_LEFT);
            $iRandNumber        = rand(1000, 9999);
            $sOrderNumber       = substr_replace($sTransactionId, $iRandNumber, 0, -9);

            $this->order_id_full = $sOrderNumber;
        }
    }

    public function updateStatus($sStatus, $sMsg = '')
    {
        $this->payment_status = $sStatus;
        $this->payment_status_msg = $sMsg;
    }
}

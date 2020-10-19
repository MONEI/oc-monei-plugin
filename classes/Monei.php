<?php namespace MONEI\MONEI\Classes;

use Cms;
use Exception;
use MONEI\MONEI\Models\Settings;
use MONEI\MONEI\Models\Order;
use Monei\MoneiClient;
use October\Rain\Argon\Argon;
use October\Rain\Support\Traits\Singleton;

class Monei
{
    use Singleton;

    const URL_CALLBACK = '/monei/callback';

    /** @var MoneiClient */
    public $obMonei;

    /** @var array */
    public $arConfig;

    /** @var array */
    public $arParams;

    /** @var string */
    public $sUrlCallback;

    /** @var bool */
    public $bDebug;

    public function __construct()
    {
        $this->arConfig = Settings::instance()->configArray();
        $this->obMonei = new MoneiClient($this->getApiKey());
        $this->sUrlCallback = Cms::url(self::URL_CALLBACK);
        $this->bDebug = $this->arConfig['is_debug_mode'] ?? false;
    }

    /*
     * Set parameters.
     */
    public function setParameters(array $arParams)
    {
        $this->arParams = $arParams;
    }

    /*
     * Get API key depending on test mode is on/off.
     *
     * @return string
     */
    public function getApiKey()
    {
        $sApiKey = $this->arConfig['api_key'];

        if ($this->arConfig['is_test_mode']) {
            $sApiKey = $this->arConfig['api_key_test'];
        }

        return $sApiKey;
    }

    /*
     * Make request to pay.
     *
     * @param \MONEI\MONEI\Models\Order $obOrder (required)
     * @param array $arParams
     */
    public function pay($obOrder, $arParams = [])
    {
        try {
            $arParams = $this->getArgs($obOrder, $arParams);

            return $this->obMonei->payments->create($arParams);
        } catch (Exception $e) {
            trace_log('Error while creating payment: ', $e->getMessage());
        }
    }

    /*
     * Make request to pay.
     *
     * @param \MONEI\MONEI\Models\Order $obOrder (required)
     * @param array $arParams
     * @return \OpenAPI\Client\Model\Payment
     */
    public function refund($obOrder, $arParams = [])
    {
        try {
            $obPayment = $this->obMonei->payments->refund($obOrder->transaction_id, $arParams);

            $this->storeRefund($obOrder, $obPayment);

            return $obPayment;
        } catch (Exception $e) {
            trace_log('Error while creating payment: ', $e->getMessage());
        }
    }

    /*
     * Store refund into DB and update Order status.
     *
     * @param \MONEI\MONEI\Models\Order $obOrder (required)
     * @param array $arParams
     */
    public function storeRefund($obOrder, $obPayment)
    {
        $obOrder->payment_status = $obPayment->getStatus();

        $arRefund = [
            'date' => Argon::now()->format('Y-m-d H:i:s'),
            'amount' => $obPayment->getLastRefundAmount(),
        ];

        $arRefunds = $obOrder->getRefunds();

        $arRefunds[] = $arRefund;
        $obOrder->refunds = $arRefunds;

        // Refund completed.
        $obOrder->save();
    }

    /**
     * Get arguments
     *
     * @access public
     * @return array
     */
    public function getArgs($obOrder, $arParams = [])
    {
        $arArgs = array_merge([
            'amount'        => $obOrder->total,
            'orderId'       => (string)$obOrder->order_id_full,
            'description'   => $obOrder->description,
            'currency'      => $obOrder->currency ?: $this->arConfig['currency'],
            'customer'      => [
                'email'         => $obOrder->email,
                'name'          => $obOrder->name,
                'phone'         => $obOrder->phone,
            ],
            'callbackUrl'   => $this->sUrlCallback,
        ], $arParams);

        $this->logOrderArgs($obOrder, $arArgs);

        return $arArgs;
    }

    /*
     * Log order arguments.
     */
    private function logOrderArgs($obOrder, $arArgs, $sLogMessage = '')
    {
        if ($this->bDebug) {
            Helper::logLine($sLogMessage ?: 'Generating payment form for order ' . $obOrder->id);
            foreach ($arArgs as $sKey=>$sVal) {
                Helper::logLine($sVal, $sKey);
            }
            Helper::logLine('Password: ' . $this->arConfig['password']);
        }
    }

    /**
     * Successful Payment!
     *
     * @access public
     * @param array $posted
     * @return void
     */
    public function processSuccessRequest($arData)
    {
        $obOrder = Order::where('order_id_full', $arData['orderId'])->first();

        if (!$obOrder) {
            if ($this->bDebug) {
                Helper::logLine('Order not found with full order_id ' . $arData['orderId']);
            }
            return;
        }

        Helper::log($arData);

        if ('Transaction Approved' === $arData['message']) {
            // authorized.

            if ((int)$obOrder->total !== (int)$arData['amount']) {
                // amount does not match.
                $sMsg = sprintf(Lang::get('monei.monei::lang.validation.on_hold'), $obOrder->total, $arData['amount']);

                if ($this->bDebug) {
                    Helper::logLine($sMsg);
                }
                // Put this order on-hold for manual checking.
                $obOrder->updateStatus(Order::STATUS_ON_HOLD, $sMsg);
                $obOrder->save();
                exit;
            }

            $obDateCarbon  = Carbon::now();

            if ($this->bDebug) {
                Helper::logLine('Date: ' . $obDateCarbon->format('Y-m-d H:i:s'));
            }

            if (!empty($date)) {
                $obOrder->payment_date = $obDateCarbon;
            }

            if (!empty($arData['id'])) {
                $obOrder->transaction_id = $arData['id'];
            }

            // Payment completed.
            $obOrder->updateStatus(Order::STATUS_SUCCEEDED);
            $obOrder->save();

            if ($this->bDebug) {
                Helper::logLine('Payment complete.');
            }
        } else {
            // Tarjeta caducada.
            if ($this->bDebug) {
                trace_log($arData);
                Helper::logLine('Order cancelled by MONEI: ' . $arData['message']);
            }
            // Order cancelled.
            $obOrder->updateStatus(Order::STATUS_CANCELLED, 'Cancelled by MONEI: ' . $arData['message']);
            if (!empty($arData['id'])) {
                $obOrder->transaction_id = $arData['id'];
            }
            $obOrder->save();
        }
    }

    public function getPayment($sId)
    {
        $sURL = "https://api.monei.net/v1/payments/" . $sId;

        $aHTTP['http']['method']  = 'GET';
        $aHTTP['http']['header'] = "Authorization: " . $this->getApiKey();

        $context = stream_context_create($aHTTP);
        $contents = file_get_contents($sURL, false, $context);

        $arPayment = json_decode($contents, true);

        return $arPayment;
    }
}
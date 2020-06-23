<?php namespace MONEI\MONEI\Classes;

use Carbon\Carbon;
use Cms;
use MONEI\MONEI\Classes\Contracts\GatewayInterface;
use MONEI\MONEI\Models\Order;
use Lang;

class MONEIGateway implements GatewayInterface
{
    const URL_CHECKOUT = 'https://pay.monei.net/checkout';
    const URL_REFUND = 'https://api.monei.net/v1/refund';
    const URL_CALLBACK = '/monei/callback';
    
    const TRANSACTION_TYPE_SALE = 'sale';
    const TRANSACTION_TYPE_REFUND = 'refund';

    /** @var array */
    public $arConfig;

    /** @var array */
    public $arParams;

    /** @var array */
    protected $arPurchaseData = [];

    /** @var bool */
    public $bDebug;

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
    public function __construct(array $arConfig, $arParams = [])
    {
        $this->arConfig = $arConfig;
        $this->arParams = $arParams;

        $this->bDebug = $arConfig['is_debug_mode'] ?? false;
    }

    /**
     * Get arguments
     *
     * @access public
     * @return array
     */
    public function getArgs($obOrder)
    {
        $obOrder->setOrderIFull();
        $obOrder->save();

        $iAmount            = $obOrder->total;

        $sUrlCallback       = Cms::url(self::URL_CALLBACK);
        $sUrlCancel         = $this->arParams['url_cancel'];
        $sUrlComplete       = $this->arParams['url_complete'];

        $arMoneiArgs = [
            'account_id'       => $this->arConfig['account_id'],
            'amount'           => $iAmount,
            'currency'         => $this->arConfig['currency'],
            'order_id'         => $obOrder->order_id_full,
            'shop_name'        => $this->arConfig['shop_name'],
            'test'             => $this->arConfig['is_test_mode'],
            'transaction_type' => self::TRANSACTION_TYPE_SALE,
            'url_callback'     => $sUrlCallback,
            'url_cancel'       => $sUrlCancel,
            'url_complete'     => $sUrlComplete,
        ];
        ksort($arMoneiArgs);

        $sMessage = '';
        foreach ($arMoneiArgs as $sMoneiKey=>$sMoneiArg) {
            $sMessage .= $sMoneiKey . $sMoneiArg;
        }

        $arMoneiArgs['signature'] = hash_hmac('sha256', $sMessage, $this->arConfig['password']);

        $this->logOrderArgs($obOrder, $arMoneiArgs, $sMessage);

        return $arMoneiArgs;
    }

    private function logOrderArgs($obOrder, $arMoneiArgs, $sMessage)
    {
        if ($this->bDebug) {
            Helper::logLine('Generating payment form for order ' . $obOrder->id);
            Helper::logLine('Helping to understand the encrypted code: ');
            foreach ($arMoneiArgs as $sKey=>$sVal) {
                Helper::logLine($sVal, $sKey);
            }
            Helper::logLine('Password: ' . $this->arConfig['password']);
            Helper::logLine('concatenated: ' . $sMessage);
            Helper::logLine('sign: ' . $arMoneiArgs['signature']);
        }
    }

    /**
     * Generate the monei form
     *
     * @access public
     * @param Order $obOrder
     * @return string
     */
    public function getFormInputs($obOrder)
    {
        $arArgs = $this->getArgs($obOrder);

        $form_inputs = '';
        foreach ($arArgs as $key => $value) {
            $form_inputs .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }

        return $form_inputs;
    }

    /**
     * Check monei IPN validity
     **/
    public function checkIpnRequestIsValid()
    {
        if ($this->bDebug) {
            Helper::logLine('checking notification');
        }

        if ($sSignature = post('signature')) {
            $arPosted = array_except(post(), ['signature']);
            ksort($arPosted);

            $sMessage = '';
            foreach ($arPosted as $sKey=>$sVal) {
                $sMessage .= $sKey . $sVal;
            }

            $sPassword = $this->arConfig['password'];

            if (!empty($sMessage) && !empty($sPassword) && !empty($sSignature)) {
                $sSignatureCheck = hash_hmac('sha256', $sMessage, $sPassword);
                if ($this->bDebug) {
                    Helper::logLine('data: ' .  $sMessage);
                    Helper::logLine('signature form MONEI: ' .  $sSignature);
                    Helper::logLine('signature at Plugin: ' .  $sSignatureCheck);
                }
                if ($sSignatureCheck !== $sSignature) {
                    if ($this->bDebug) {
                        Helper::logLine('Received INVALID notification from MONEI');
                    }
                    return false;
                } else {
                    if ($this->bDebug) {
                        Helper::logLine('Correct signature');
                    }
                    $sAmount     = post('amount');
                    $iOrderId    = post('order_id');

                    $obOrder     = Order::where('order_id_full', $iOrderId)->first();

                    if (!$obOrder) {
                        if ($this->bDebug) {
                            Helper::logLine('Order not found with full order_id ' . post('order_id'));
                        }
                        return false;
                    }

                    if ((int)$sAmount === (int)$obOrder->total) {
                        if ($this->bDebug) {
                            Helper::logLine('The amount match');
                        }
                        return true;
                    } else {
                        if ($this->bDebug) {
                            Helper::logLine('The amount does not match (' . $obOrder->total . ' and ' . $sAmount . ')');
                        }
                        return false;
                    }
                }
            } else {
                if ($this->bDebug) {
                    Helper::logLine('Received INVALID notification from MONEI');
                }
                return false;
            }
        } else {
            if ($this->bDebug) {
                Helper::logLine('Received INVALID notification from MONEI');
            }
            return false;
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
        $obOrder = Order::where('order_id_full', $arData['order_id'])->first();

        if (!$obOrder) {
            if ($this->bDebug) {
                Helper::logLine('Order not found with full order_id ' . $arData['order_id']);
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
                $obOrder->updateStatus('on-hold', $sMsg);
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

            if (!empty($arData['checkout_id'])) {
                $obOrder->checkout_id = $arData['checkout_id'];
            }

            // Payment completed.
            $obOrder->updateStatus('completed');
            $obOrder->save();

            if ($this->bDebug) {
                Helper::logLine('Payment complete.');
            }
        } else {
            // Tarjeta caducada.
            if ($this->bDebug) {
                Helper::logLine('Order cancelled by MONEI: ' . $arData['message']);
            }
            // Order cancelled.
            $obOrder->updateStatus('cancelled', 'Cancelled by MONEI: ' . $arData['message']);
            $obOrder->save();
        }
    }
}
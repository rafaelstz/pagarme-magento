<?php

class PagarMe_Checkout_Model_Checkout extends
 Mage_Payment_Model_Method_Abstract
{
    const PAGARME_CHECKOUT_BOLETO      = 'pagarme_checkout_boleto';

    const PAGARME_CHECKOUT_CREDIT_CARD = 'pagarme_checkout_credit_card';

    /**
     * @var string
     */
    protected $_code                   = 'pagarme_checkout';

    /**
     * @var boolean
     */
    protected $_isGateway              = true;

    /**
     * @var boolean
     */
    protected $_canAuthorize           = true;

    /**
     * @var boolean
     */
    protected $_canCapture             = true;

    /**
     * @var boolean
     */
    protected $_canRefund              = true;

    /**
     * @var boolean
     */
    protected $_canUseForMultishipping = true;

    /**
     * @var boolean
     */
    protected $_isInitializeNeeded     = false;

    /**
     * @var string
     */
    protected $_formBlockType          = 'pagarme_checkout/form_checkout';

    /**
     * @codeCoverageIgnore
     * @return \PagarMe\Sdk\PagarMe
     */
    public function getPagarMeSdk()
    {
        if (is_null($this->pagarMeSdk)) {
            $this->pagarMeSdk = Mage::getModel('pagarme_core/sdk_adapter')
                ->getPagarMeSdk();
        }

        return $this->pagarMeSdk;
    }

    /**
     * @param \PagarMe\Sdk\PagarMe $pagarMeSdk
     * @return void
     */
    public function setPagarMeSdk(\PagarMe\Sdk\PagarMe $pagarMeSdk)
    {
        $this->pagarMeSdk = $pagarMeSdk;
    }

    /**
     * @param array $data
     *
     * @return PagarMe_Checkout_Model_Checkout
     */
    public function assignData($data)
    {
        $paymentMethod = $this->code . '_' . $data['payment_method'];
        $token = $data['token'];

        $additionalInfoData = [
            'pagarme_payment_method' => $paymentMethod,
            'token' => $token
        ];

        $this->getInfoInstance()->setAdditionalInformation($additionalInfoData);

        return $this;
    }

    /**
     * @param Varien_Object $payment
     * @return PagarMe_Checkout_Model_Checkout
     */
    public function authorize(Varien_Object $payment)
    {
        $infoInstance = $this->getInfoInstance();
        $token = $infoInstance->getAdditionalInformation('token');
        $paymentMethod = $infoInstance->getAdditionalInformation(
            'payment_method'
        );

        $infoInstance->unsAdditionalInformation('token');

        $transaction = $this->getPagarMeSdk()
            ->transaction()
            ->get($token);

        $order = $payment->getOrder();

        $transactionData = [
            'pagarme_transaction_id' => $transaction->getId(),
            'store_order_id'         => $order->getId(),
            'store_increment_id'     => $order->getIncrementId()
        ];

        if ($paymentMethod == self::PAGARME_CHECKOUT_BOLETO) {
            $transactionData['pagarme_boleto_url'] = $transaction
                ->getBoletoUrl();
        }

        $infoInstance->setAdditionalInformation(
            array_merge(
                $infoInstance->getAdditionalInformation(),
                $transactionData
            )
        );

        Mage::getModel('pagarme_core/transaction')
            ->setTransactionId($transaction->getId())
            ->setOrderId($orderId)
            ->save();

        return $this;
    }
}

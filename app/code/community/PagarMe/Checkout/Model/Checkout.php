<?php

class PagarMe_Checkout_Model_Checkout extends Mage_Payment_Model_Method_Abstract
{
    const PAGARME_CHECKOUT_BOLETO = 'pagarme_checkout_boleto';

    /**
     * @var string
     */
    protected $_code = 'pagarme_checkout';
    /**
     * @var bool
     */
    protected $_isGateway = true;
    /**
     * @var bool
     */
    protected $_canAuthorize = true;
    /**
     * @var bool
     */
    protected $_canCapture = true;
    /**
     * @var bool
     */
    protected $_canRefund = true;
    /**
     * @var bool
     */
    protected $_canUseForMultishipping = true;
    /**
     * @var bool
     */
    protected $_isInitializeNeeded = false;
    /**
     * @var string
     */
    protected $_formBlockType = 'pagarme_checkout/form_checkout';

    /**
     * @param array $data
     *
     * @return $this
     */
    public function assignData($data)
    {
        $info = $this->getInfoInstance();

        $customerData = Mage::helper('pagarme_core')
            ->prepareCustomerData($data);

        $customer = Mage::helper('pagarme_core')->buildCustomer($customerData);

        $info->setAdditionalInformation([
            'pagarme_payment_method' =>
                $this->_code .'_' . $data['pagarme_checkout_payment_method'],
            'customer' => $customer,
            'token' => $data['pagarme_checkout_token']
        ]);

        return $this;
    }

    /**
     * Authorize payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @throws Exception
     *
     * @return $this
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        $infoInstance = $this->getInfoInstance();
        
        $preTransactionEntity = Mage::getModel('pagarme_core/entity_factory')
            ->prepareTransaction(
                $amount,
                $infoInstance
            );

        try {
            $transaction = Mage::getModel('pagarme_core/service_transaction')
                ->capture($preTransactionEntity);
        } catch (\Exception $exception) {
            throw $exception;
        }

        $order = $payment->getOrder();

        $infoInstance->unsAdditionalInformation('customer');

        $infoInstance->setAdditionalInformation(
            $this->extractAdditionalInfo($infoInstance, $transaction, $order)
        );

        Mage::getModel('pagarme_core/transaction')
            ->setTransactionId($transaction->getId())
            ->setOrderId($order->getId())
            ->save();

        return $this;
    }

    /**
     * @param type $infoInstance
     * @param \PagarMe\Sdk\Transaction\AbstractTransaction $transaction
     * @param type $order
     *
     * @return array
     */
    private function extractAdditionalInfo($infoInstance, $transaction, $order)
    {
        $data = [
                'pagarme_transaction_id' => $transaction->getId(),
                'store_order_id'         => $order->getId(),
                'store_increment_id'     => $order->getIncrementId()
            ];

        if ($transaction instanceof PagarMe\Sdk\Transaction\BoletoTransaction) {
            $data['pagarme_boleto_url'] = $transaction->getBoletoUrl();
        }

        return array_merge(
            $infoInstance->getAdditionalInformation(),
            $data
        );
    }
}

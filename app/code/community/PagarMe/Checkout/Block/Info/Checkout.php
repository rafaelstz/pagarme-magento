<?php

class PagarMe_Checkout_Block_Info_Checkout extends Mage_Payment_Block_Info
{
    protected $transaction;
    
    const PAYMENT_METHOD_CREDIT_CARD_LABEL = 'Cartão de Crédito';
    const PAYMENT_METHOD_BOLETO_LABEL = 'Boleto';

    /**
     * @codeCoverageIgnore
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('pagarme/checkout/order_info/payment_details.phtml');
    }

    /**
     * @codeCoverageIgnore
     *
     * @return PagarMe_Core_Model_Transaction
     */
    public function getTransaction()
    {
        if (is_null($this->transaction)) {
            $this->transaction =  \Mage::getModel('pagarme_core/service_order')
                ->getTransactionByOrderId(
                    $this->getInfo()
                        ->getOrder()
                        ->getId()
                );
        }

        return $this->transaction;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->transaction->getPaymentMethod()
            == \PagarMe\Sdk\Transaction\CreditCardTransaction::PAYMENT_METHOD
            ? self::PAYMENT_METHOD_CREDIT_CARD_LABEL
            : self::PAYMENT_METHOD_BOLETO_LABEL;
    }
}

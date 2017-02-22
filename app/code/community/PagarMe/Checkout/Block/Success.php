<?php

class PagarMe_Checkout_Block_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function isBoletoPayment()
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId(
            $this->getOrderId()
        );

        $additionalInfo = $order->getPayment()->getAdditionalInformation();

        if ($additionalInfo['pagarme_payment_method'] === PagarMe_Checkout_Model_Checkout::PAGARME_CHECKOUT_BOLETO) {
            return true;
        }

        return false;
    }

    public function getBoletoUrl()
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId(
            $this->getOrderId()
        );

        $additionalInfo = $order->getPayment()->getAdditionalInformation();

        return $additionalInfo['pagarme_boleto_url'];
    }
}

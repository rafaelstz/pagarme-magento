<?php

class PagarMe_Checkout_Block_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function isBoletoPayment()
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId(
            $this->getOrderId()
        );

        $additionalInfo = $order->getPayment()->getAdditionalInformation();

        if ($additionalInfo['payment_method'] === 'boleto') {
            return true;
        }

        return false;
    }
}

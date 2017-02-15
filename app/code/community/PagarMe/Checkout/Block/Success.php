<?php

class PagarMe_Checkout_Block_Success extends Mage_Checkout_Block_Onepage_Success
{
    public function isBoletoPayment()
    {
        var_dump($this->getOrderId());
        die();
    }
}

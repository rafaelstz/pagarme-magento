<?php

namespace PagarMe\Magento\Test\Helper;

trait PagarMeCheckoutSwitch
{
    protected function enablePagarmeCheckout()
    {
        $this->changePagarmeCheckout(true);
    }

    protected function disablePagarmeCheckout()
    {
        $this->changePagarmeCheckout(false);
    }

    protected function changePagarmeCheckout($value)
    {
        \Mage::getConfig()->saveConfig(
            'payment/pagarme_settings/active',
            $value
        );

        \Mage::getConfig()->saveConfig(
            'payment/pagarme_checkout/active',
            $value
        );

        \Mage::getConfig()->cleanCache();
    }
}

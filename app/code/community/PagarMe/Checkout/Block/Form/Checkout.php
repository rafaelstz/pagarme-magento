<?php

class PagarMe_Checkout_Block_Form_Checkout extends Mage_Payment_Block_Form
{
    const TEMPLATE = 'pagarme/form/checkout.phtml';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE);
        $this->customer = Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getEncryptionKey()
    {
        return Mage::getStoreConfig('payment/pagarme_settings/encryption_key');
    }
}

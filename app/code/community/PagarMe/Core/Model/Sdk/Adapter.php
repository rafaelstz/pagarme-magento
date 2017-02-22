<?php

class PagarMe_Core_Model_Sdk_Adapter extends Mage_Core_Model_Abstract 
{
    private $pagarMeSdk;

    public function _construct()
    {
        parent::_construct();

        $apiKey =  Mage::getStoreConfig('payment/pagarme_settings/api_key');
        $this->pagarMeSdk = new \PagarMe\Sdk\PagarMe($apiKey);
    }

    public function getPagarMeSdk()
    {
        return $this->pagarMeSdk;
    }
}
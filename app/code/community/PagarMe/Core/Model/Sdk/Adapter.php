<?php

class PagarMe_Core_Model_Sdk_Adapter extends Mage_Core_Model_Abstract 
{
    private $pagarMeSdk;

    public function _construct()
    {
        parent::_construct();
        $this->pagarMeSdk = new \PagarMe\Sdk\PagarMe('ak_test_fEdp850tlXjulV60cGxnUtD3hLtl7C');
    }

    public function getPagarMeSdk()
    {
        return $this->pagarMeSdk;
    }
}
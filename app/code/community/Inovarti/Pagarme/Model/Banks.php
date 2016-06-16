<?php

class Inovarti_Pagarme_Model_Banks extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Banks constructor.
     */
    protected function __construct()
    {
        return $this->_init('pagarme/banks');
    }
}
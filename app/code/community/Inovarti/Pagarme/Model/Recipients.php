<?php

class Inovarti_Pagarme_Model_Recipients extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Recipients constructor.
     */
    protected function __construct()
    {
        return $this->_init('pagarme/recipients');
    }
}
<?php

class Inovarti_Pagarme_Model_Recipients extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Recipients constructor.
     */
    protected function _construct()
    {
        return $this->_init('pagarme/recipients');
    }
}
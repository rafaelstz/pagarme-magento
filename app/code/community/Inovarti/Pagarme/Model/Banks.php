<?php

class Inovarti_Pagarme_Model_Banks extends Mage_Core_Model_Abstract
{
    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _construct()
    {
        return $this->_init('pagarme/banks');
    }
}
<?php

class Inovarti_Pagarme_Model_Splitrules extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Splitrules constructor.
     */
    protected function _construct()
    {
        return $this->_init('pagarme/splitrules');
    }

    public function validate() {
        $errors = [];

        $amount = $this->getAmount();

        if(!is_numeric($amount)
            || $amount > 100
            || $amount < 0) {
            $errors[] = 'Invalid value for \'amount\'';
        }

        return $errors;
    }
}

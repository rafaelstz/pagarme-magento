<?php

class Inovarti_Pagarme_Model_Marketplacemenu extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Splitrules constructor.
     */
    protected function _construct()
    {
        return $this->_init('pagarme/marketplacemenu');
    }

    /**
     * @param string $productSku
     * @return boolean
     */
    public function productIsAssociatedWithASplitRule($productSku) {
        return Mage::getModel('pagarme/marketplacemenu')
            ->getCollection()
            ->addFieldToFilter('sku', $productSku)
            ->count() > 0;
    }

    /**
     * @return boolean
     */
    public function validate()
    {
        $errors = array();

        if($this->productIsAssociatedWithASplitRule($this->getSku())) {
            $errors[] = 'This product already has an associated split rule.';
        }

        return $errors;
    }
}

<?php

class Inovarti_Pagarme_Model_System_Config_Validator_LiableValidator extends Mage_Core_Model_Config_Data
{
    public function save() {
        $marketPlaceIsResponsibleForChargeback = $this->getValue();

        if(!$marketPlaceIsResponsibleForChargeback) {
            $splitRuleCollection = Mage::getModel('pagarme/splitrules')
                ->getCollection()
                ->addFieldToFilter('liable', array('eq', '0'));
            $splitRuleCollection->getSelect()
                ->join(array('marketplace_menu' => 'pagarme_marketplace_menu'),
                    'main_table.recipient_id = marketplace_menu.recipient_id');

            $qtdRulesThatAreNotResponsibleForChargeback = $splitRuleCollection->count();

            if($qtdRulesThatAreNotResponsibleForChargeback > 0) {
                Mage::throwException('More than one recipients are not responsible for chargeback');
            }
        }

        return parent::save();
    }
}

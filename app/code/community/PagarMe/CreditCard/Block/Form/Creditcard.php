<?php

use PagarMe_CreditCard_Model_CurrentOrder as CurrentOrder;

class PagarMe_Creditcard_Block_Form_CreditCard extends Mage_Payment_Block_Form_Cc
{
    const TEMPLATE = 'pagarme/form/credit_card.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate(self::TEMPLATE);
    }

    public function getEncryptionKey()
    {
        return Mage::getStoreConfig(
            'payment/pagarme_configurations/general_encryption_key'
        );
    }

    public function getInstallments()
    {
        $quote = Mage::getModel('checkout/session')->getQuote();
        $pagarMeSdk = Mage::getModel('pagarme_core/sdk_adapter');
        $currentOrder = new CurrentOrder(
            $quote,
            $pagarMeSdk
        );

        $interestRate = Mage::getStoreConfig('payment/pagarme_configurations/creditcard_interest_rate');
        $maxInstallments = Mage::getStoreConfig('payment/pagarme_configurations/creditcard_max_installments');
        $freeInstallments = Mage::getStoreConfig('payment/pagarme_configurations/creditcard_free_installments');
        return $currentOrder->calculateInstallments(
            $maxInstallments,
            $freeInstallments,
            $interestRate
        );
    }
}


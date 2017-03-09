<?php

class PagarMe_Core_Model_Entity_Factory
{
    /**
     * @param float $amount
     * @param Mage_Sales_Model_Order_Payment $infoInstance
     *
     * @return PagarMe_Core_Model_Entity_EntityInterface
     */
    public function prepareTransaction(
        $amount,
        $infoInstance
    ) {
        switch ($infoInstance->getAdditionalInformation('pagarme_payment_method')) {
            case PagarMe_Core_Model_Entity_CreditCard::PAGARME_PAYMENT_METHOD:
                $preTransaction = Mage::getModel(
                    'pagarme_core/entity_creditcard'
                );

                $preTransaction->setToken(
                    $infoInstance->getAdditionalInformation('token')
                );
                $preTransaction->setAmount(
                    Mage::helper('pagarme_core')->parseAmountToInteger($amount)
                );
                $preTransaction->setPostBackUrl(
                    Mage::getUrl('pagarme/transaction_creditcard/postback')
                );

                return $preTransaction;
            case PagarMe_Core_Model_Entity_Boleto::PAGARME_PAYMENT_METHOD:
                $preTransaction = Mage::getModel(
                    'pagarme_core/entity_boleto'
                );

                $preTransaction->setToken(
                    $infoInstance->getAdditionalInformation('token')
                );
                $preTransaction->setAmount(
                    Mage::helper('pagarme_core')->parseAmountToInteger($amount)
                );
                $preTransaction->setPostBackUrl(
                    Mage::getUrl('pagarme/transaction_boleto/postback')
                );

                return $preTransaction;
        }
    }
}

<?php

trait PagarMe_Core_Trait_ConfigurationsAccessor
{

    private function isTransparentCheckoutActiveStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/transparent_active'
        );
    }

    private function getCreditcardTitleStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/creditcard_title'
        );
    }

    private function getMaxInstallmentStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/creditcard_max_installments'
        );
    }

    public function getEncryptionKeyStoreConfig()
    {
        return Mage::getStoreConfig(
            'pagarme_configurations/general_encryption_key'
        );
    }

    private function getFreeInstallmentStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/creditcard_free_installments'
        );
    }

    private function getInterestRateStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/creditcard_interest_rate'
        );
    }

    private function getConfigurationWithName($name)
    {
        return Mage::getStoreConfig("payment/{$name}");
    }

}

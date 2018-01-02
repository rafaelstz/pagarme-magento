<?php

trait PagarMe_Core_Trait_ConfigurationsAccessor
{
    private function getMaxInstallmentStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/max_installments'
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
            'pagarme_configurations/free_installments'
        );
    }

    private function getInterestRateStoreConfig()
    {
        return $this->getConfigurationWithName(
            'pagarme_configurations/interest_rate'
        );
    }

    private function getConfigurationWithName($name)
    {
        return Mage::getStoreConfig("payment/{$name}");
    }

}

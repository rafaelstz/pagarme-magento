<?php

    use PagarMe_Core_Model_Exception_ConfigurationsDoesntExists as ConfigurationDoesntExists;

trait PagarMe_Core_Trait_ConfigurationsAccessor
{

    private function isTransparentCheckoutActiveStoreConfig()
    {
        return (bool) $this->getConfigurationWithName(
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
        return (int) $this->getConfigurationWithName(
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
        return (int) $this->getConfigurationWithName(
            'pagarme_configurations/creditcard_free_installments'
        );
    }

    private function getInterestRateStoreConfig()
    {
        return (float) $this->getConfigurationWithName(
            'pagarme_configurations/creditcard_interest_rate'
        );
    }

    private function getConfigurationWithName($name)
    {
        Mage::log('olha isso');
        Mage::log(Mage::getStoreConfig('nem_existe') == null);
        return Mage::getStoreConfig("payment/{$name}");
    }

}

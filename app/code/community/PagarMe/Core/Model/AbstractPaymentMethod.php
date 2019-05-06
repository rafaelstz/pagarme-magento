<?php

abstract class PagarMe_Core_Model_AbstractPaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    use PagarMe_Core_Trait_ConfigurationsAccessor;

    /**
     * @var boolean
     */
    protected $_isInitializeNeeded = true;

    /**
     * Returns payment method code for postback route
     *
     * @return string
     */
    abstract protected function getPostbackCode();

    /**
     * @codeCoverageIgnore
     * @return string
     */
    protected function getUrlForPostback()
    {
        $postbackUrl = Mage::getBaseUrl();
        $developmentPostbackUrl = $this->getDevelopmentPostbackUrl();

        if ($this->isDeveloperModeEnabled() && $developmentPostbackUrl !== '') {
            $postbackUrl = $developmentPostbackUrl;
        }

        $postbackUrl .=  sprintf(
            'pagarme_core/%s/postback',
            $this->getPostbackCode()
        );

        return $postbackUrl;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        var_dump($this->_code, $this->getActiveTransparentPaymentMethod(), $this->isTransparentCheckoutActiveStoreConfig());
        return $this->isTransparentCheckoutActiveStoreConfig()
            && strpos($this->getActiveTransparentPaymentMethod(), $this->_code);
    }
}

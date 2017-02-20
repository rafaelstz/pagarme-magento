<?php

class PagarMe_Checkout_Model_Checkout extends Mage_Payment_Model_Method_Abstract
{
    /** @var string */
    protected $_code                   = 'pagarme_checkout';

    /** @var boolean */
    protected $_isGateway              = true;

    /** @var boolean */
    protected $_canAuthorize           = true;

    /** @var boolean */
    protected $_canCapture             = true;

    /** @var boolean */
    protected $_canRefund              = true;

    /** @var boolean */
    protected $_canUseForMultishipping = true;

    /** @var string */
    protected $_formBlockType          = 'pagarme_checkout/form_checkout';

    /** @var string */
    protected $_infoBlockType          = 'pagarme_checkout/info_checkout';

    public function getPagarMeSdk()
    {
        return Mage::getModel('pagarme_core/sdk_adapter')
            ->getPagarMeSdk();
    }

    /**
     * @codeCoverageIgnore
     */
    public function assignData($data)
    {
    }

    /**
     * Authorize payment
     *
     * @param Varien_Object
     *
     * @return void
     */
    public function authorize(Varien_Object $payment)
    {
        $this->getPagarMeSdk()->transaction()->createTransaction();
    }

    /**
     * Capture payment
     *
     * @param Varien_Object
     *
     * @return void
     */
    public function capture(Varien_Object $payment)
    {
    }
}

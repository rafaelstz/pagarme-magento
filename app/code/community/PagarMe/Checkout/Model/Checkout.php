<?php

class PagarMe_Checkout_Model_Checkout extends Mage_Payment_Model_Method_Abstract
{
    use \PagarMe\Sdk\Customer\CustomerBuilder;

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

    /** @var boolean */
    protected $_isInitializeNeeded      = false;

    /** @var string */
    protected $_formBlockType          = 'pagarme_checkout/form_checkout';

    /** @var string */
    protected $_infoBlockType          = 'pagarme_checkout/info_checkout';

    public function getPagarMeSdk()
    {
        if(is_null($this->pagarMeSdk)) {
            $this->pagarMeSdk = Mage::getModel('pagarme_core/sdk_adapter')
                ->getPagarMeSdk();
        }

        return $this->pagarMeSdk;
    }

    public function setPagarMeSdk(\PagarMe\Sdk\PagarMe $pagarMeSdk)
    {
        $this->pagarMeSdk = $pagarMeSdk;
    }

    /**
     * @codeCoverageIgnore
     */
    public function assignData($data)
    {
        $info = $this->getInfoInstance();

        $info->setAdditionalInformation(
            [
                'pagarme_checkout_payment_method' => $data['pagarme_checkout_payment_method']
            ]
        );

        $customerData = Mage::helper('pagarme_core')
            ->prepareCustomerData($data);

        $customer = $this->buildCutomer($customerData);

        $info->setCustomer($customer);

        return $this;
    }

    /**
     * Authorize payment
     *
     * @param Varien_Object
     *
     * @return void
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);

        $infoInstance = $this->getInfoInstance();
        $customer = $infoInstance->getCustomer();

        $this->getPagarMeSdk()
            ->transaction()
            ->boletoTransaction(
                $amount,
                $customer,
                $postBackUrl
            );
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

<?php

class PagarMe_Checkout_Block_Form_Checkout extends Mage_Payment_Block_Form
{
    const TEMPLATE = 'pagarme/form/checkout.phtml';

    /**
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE);
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getEncryptionKey()
    {
        return Mage::getStoreConfig('payment/pagarme_settings/encryption_key');
    }

    /**
     * @return string
     */
    public function getCheckoutConfig()
    {
        $order = Mage::getSingleton('checkout/session')->getQuote();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $address = $customer->getDefaultBillingAddress();

        $helper = Mage::helper('pagarme_core');

        $telephone = $address->getTelephone();

        return json_encode([
            'amount' => $helper->parseAmountToInteger($order->getGrandTotal()),
            'createToken' => "false",
            'customerName' => $customer->getName(),
            'customerEmail' => $customer->getEmail(),
            'customerDocumentNumber' => $customer->getTaxvat(),
            'customerPhoneDdd' => $helper->getDddFromPhoneNumber($telephone),
            'customerPhoneNumber' => $helper->getPhoneWithoutDdd($telephone),
            'customerAddressZipcode' => $address->getPostcode(),
            'customerAddressStreet' => $address->getStreet(1),
            'customerAddressStreetNumber' => $address->getStreet(2),
            'customerAddressComplementary' => $address->getStreet(3),
            'customerAddressNeighborhood' => $address->getStreet(4),
            'customerAddressCity' => $address->getCity(),
            'customerAddressState' => $address->getRegion()
        ]);
    }
}

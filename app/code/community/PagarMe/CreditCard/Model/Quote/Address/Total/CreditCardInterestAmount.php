<?php

class PagarMe_CreditCard_Model_Quote_Address_Total_CreditCardInterestAmount
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $address->setDiscountAmount(0);
        $address->setBaseDiscountAmount(0);
        if ($this->shouldDoSomethig($address)) {
            $paymentMethodParameters = Mage::app()->getRequest()->getPost()['payment'];
            $installments = $paymentMethodParameters['installments'];
            $this->_addAmount(12345);
            $this->_addBaseAmount(12345);
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->shouldDoSomethig($address)) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => 'Mad Skillz',
                'value' => 12345
            ));
        }

        return $this;
    }

    /**
     * The class is called for the two addresses (billing and shipping)
     * This prevents the method from adding the interest two times
     */
    private function shouldDoSomethig(
        Mage_Sales_Model_Quote_Address $address
    ) {
        $quote = $address->getQuote();
        $paymentMethodUsedWasPagarme = $quote->getPayment()->getMethod() == 'pagarme_creditcard';

        $addressUsedIsShipping = $address->getAddressType() == 'shipping';
        return $paymentMethodUsedWasPagarme && $addressUsedIsShipping;
    }
}

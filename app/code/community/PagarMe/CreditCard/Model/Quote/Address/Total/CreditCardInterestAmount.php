<?php

class PagarMe_CreditCard_Model_Quote_Address_Total_CreditCardInterestAmount
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        //The method is run for the two addresses (billing and shipping)
        //This prevents the method from adding the interest two times
        if ($address->getAddressType() == 'shipping') {
            $quote = $address->getQuote();
            $paymentMethod = $quote->getPayment()->getMethod();
            if ($paymentMethod == 'pagarme_creditcard') {
                parent::collect($address);
                $address->setDiscountAmount(0);
                $address->setBaseDiscountAmount(0);
                $this->_addAmount(12345);
                $this->_addBaseAmount(12345);
            }
        }
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getAddressType() == 'shipping')
        {
            $quote = $address->getQuote();
            $paymentMethod = $quote->getPayment()->getMethod();
            if ($paymentMethod == 'pagarme_creditcard') {
                $address->addTotal(array(
                    'code' => $this->getCode(),
                    'title' => 'Mad Skillz',
                    'value' => 12345
                ));
            }
        }
        return $this;
    }
}

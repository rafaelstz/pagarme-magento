<?php

use PagarMe_CreditCard_Model_CurrentOrder as CurrentOrder;

class PagarMe_CreditCard_Model_Quote_Address_Total_CreditCardInterestAmount
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    use PagarMe_Core_Trait_ConfigurationsAccessor;

    private $interestValue;

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if ($this->shouldDoSomethig($address)) {
            $address->setDiscountAmount(0);
            $address->setBaseDiscountAmount(0);
            $paymentMethodParameters = Mage::app()->getRequest()->getPost()['payment'];
            $this->interestValue = $this->interestAmountInReals(
                Mage::getSingleton('checkout/session')->getQuote(),
                $paymentMethodParameters
            );

            $this->_addAmount($this->interestValue);
            $this->_addBaseAmount($this->interestValue);

            Mage::log('Calculed!');
            Mage::log('Payment Parameters: ' . json_encode($paymentMethodParameters));
        }
        Mage::log('collect!');
        Mage::log($this->interestValue);

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        Mage::log('fetch!');
        Mage::log($this->interestValue);
        if ($this->shouldDoSomethig($address)) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => __('Installments related Interest'),
                'value' => $this->interestValue
            ));
        }

        return $this;
    }

    private function interestAmountInReals($quote, $paymentMethodParameters)
    {
        $pagarMeSdk = Mage::getModel('pagarme_core/sdk_adapter');
        $currentQuote = new CurrentOrder($quote, $pagarMeSdk);
        $calculedInstallments = $currentQuote->calculateInstallments(
            $this->getMaxInstallmentStoreConfig(),
            $this->getFreeInstallmentStoreConfig(),
            $this->getInterestRateStoreConfig()
        );

        $choosedInstallmentsValue = $paymentMethodParameters['installments'];
        $installmentsInfo = $calculedInstallments[$choosedInstallmentsValue];
        $valueWithInterestInCents = $installmentsInfo['total_amount'];

        $helper = Mage::helper('pagarme_core');
        $interestInCents = $valueWithInterestInCents - $currentQuote
            ->productsTotalValueInCents();
        return $helper->parseAmountToFloat($interestInCents);
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

        $interestRateIsntZero = $this->getInterestRateStoreConfig() > 0;
        return $paymentMethodUsedWasPagarme && $addressUsedIsShipping
            && $interestRateIsntZero;
    }
}

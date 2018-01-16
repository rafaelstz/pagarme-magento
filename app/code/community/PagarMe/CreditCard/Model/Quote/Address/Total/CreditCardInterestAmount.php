<?php

use PagarMe_Core_Model_CurrentOrder as CurrentOrder;

class PagarMe_CreditCard_Model_Quote_Address_Total_CreditCardInterestAmount
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    use PagarMe_Core_Trait_ConfigurationsAccessor;

    private $interestValue;

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if ($this->shouldDoSomethig($address)) {
            $paymentMethodParameters = Mage::app()->getRequest()->getPost()['payment'];
            $address->setDiscountAmount(0);
            $address->setBaseDiscountAmount(0);
            $this->interestValue = $this->interestAmountInReals(
                Mage::getSingleton('checkout/session')->getQuote(),
                $paymentMethodParameters
            );

            $this->_addAmount($this->interestValue);
            $this->_addBaseAmount($this->interestValue);
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
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

        $paymentMethodParameters = Mage::app()->getRequest()->getPost();
        $requestWasFromAfterPaymentMethod = array_key_exists('payment', $paymentMethodParameters)
            && array_key_exists('installments', $paymentMethodParameters['payment']);

        $interestRateIsntZero = $this->getFreeInstallmentStoreConfig() > 0;

        $installments = !$requestWasFromAfterPaymentMethod ? -1 :
            $paymentMethodParameters['payment']['installments'];
        $paymentIsntInterestFree = $installments > $this->getFreeInstallmentStoreConfig();

        return $paymentMethodUsedWasPagarme && $addressUsedIsShipping
            && $interestRateIsntZero && $requestWasFromAfterPaymentMethod
            && $paymentIsntInterestFree;
    }
}

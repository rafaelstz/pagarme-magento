<?php

class Inovarti_Pagarme_Model_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'fee';

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this|bool
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        $this->_setAmount(0);
        $this->_setBaseAmount(0);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $quote = Mage::helper('checkout')->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();

        $baseSubtotalWithDiscount = Mage::helper('pagarme')->getBaseSubtotalWithDiscount();
        $shippingAmount = $quote->getShippingAddress()->getShippingAmount();
        $total = $baseSubtotalWithDiscount + $shippingAmount;

        $post = Mage::app()->getRequest()->getPost();

        if ($paymentMethod == 'pagarme_checkout') {
            $payment_installment = $post['payment']['pagarme_checkout_installments'] > 1 ? $post['payment']['pagarme_checkout_installments'] : $payment_installment;
        } elseif ($paymentMethod == 'pagarme_cc') {
            $payment_installment = $post['payment']['installments'] > 1 ? $post['payment']['installments'] : $payment_installment;
        }

        if ($this->mustCalculateInterestForPaymentMethod($paymentMethod)) {
            $installmentConfig = $this->getInstallmentConfig($paymentMethod);
            $interestFeeAmount = $this->getInterestFeeAmount($total, $payment_installment, $installmentConfig) / 100;

            $address->setFeeAmount($interestFeeAmount);
            $quote->setFeeAmount($interestFeeAmount);
            $quote->setBaseFeeAmount($total);
        }

        $address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getFeeAmount();

        if (!$amount) {
            return $this;
        }

        $address->addTotal(array(
            'code' => $this->getCode(),
            'title' => Mage::helper('pagarme')->__('Fee'),
            'value'=> $amount,
        ));

        return $this;
    }

    private function mustCalculateInterestForPaymentMethod($paymentMethod)
    {
        return $paymentMethod == 'pagarme_checkout' || $paymentMethod == 'pagarme_cc';
    }

    private function getInstallmentConfig($paymentMethod)
    {
        if ($paymentMethod == 'pagarme_checkout') {
            return $this->getPagarMeCheckoutInstallmentConfig();
        } elseif ($paymentMethod == 'pagarme_cc') {
            return $this->getPagarMeCcInstallmentConfig();
        }
        return null;
    }

    private function getPagarMeCheckoutInstallmentConfig()
    {
        $config = new Varien_Object();
        $config->setMaxInstallments((int) Mage::getStoreConfig('payment/pagarme_checkout/max_installments'));
        $config->setFreeInstallments((int) Mage::getStoreConfig('payment/pagarme_checkout/free_installments'));
        $config->setInterestRate((float) Mage::getStoreConfig('payment/pagarme_checkout/interest_rate'));

        return $config;
    }

    private function getPagarMeCcInstallmentConfig()
    {
        $config = new Varien_Object();
        $config->setMaxInstallments((int) Mage::getStoreConfig('payment/pagarme_cc/max_installments'));
        $config->setFreeInstallments((int) Mage::getStoreConfig('payment/pagarme_cc/free_installments'));
        $config->setInterestRate((float) Mage::getStoreConfig('payment/pagarme_cc/interest_rate'));

        return $config;
    }

    /**
     * @param $collection
     * @param $payment_installment
     * @param $total
     * @param $quote
     * @param $address
     */
    private function getInterestFeeAmount($total, $qtyInstallments, $installmentConfig)
    {
        $total = Mage::helper('pagarme')->formatAmount($total);
        $creditCard = Mage::getModel('pagarme/cc');

        $interestFeeAmount = $creditCard->calculateInterestFeeAmount($total, $qtyInstallments, $installmentConfig);

        return $interestFeeAmount;
    }

    /**
     * @param $total
     * @param $minInstallmentValue
     * @param $maxInstallments
     * @return float|int
     */
    private function getMaxInstallments($total, $minInstallmentValue, $maxInstallments)
    {
        $numberInstallments = floor($total / $minInstallmentValue);

        if ($numberInstallments > $maxInstallments) {
            return $maxInstallments;
        }

        return 1;
    }
}

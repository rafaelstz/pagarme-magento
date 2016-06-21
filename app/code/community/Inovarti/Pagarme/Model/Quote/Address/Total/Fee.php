<?php

class Inovarti_Pagarme_Model_Quote_Address_Total_Fee
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_code = 'fee';

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this|bool
     */
    public function collect (Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        $this->_setAmount (0);
        $this->_setBaseAmount (0);

        $items = $this->_getAddressItems($address);
        if (!count ($items)) return $this;

        $quote = Mage::helper('checkout')->getQuote();
        $payment = $quote->getPayment()->getMethod();

        $payment_installment = 0;

        if ($payment == 'pagarme_checkout') {

            $maxInstallments = (int) Mage::getStoreConfig('payment/pagarme_checkout/max_installments');
            $minInstallmentValue = (float) Mage::getStoreConfig('payment/pagarme_checkout/min_installment_value');
            $interestRate = (float) Mage::getStoreConfig('payment/pagarme_checkout/interest_rate');
            $freeInstallments = (int) Mage::getStoreConfig('payment/pagarme_checkout/free_installments');

            $baseSubtotalWithDiscount = Mage::helper('pagarme')->getBaseSubtotalWithDiscount();
            $shippingAmount = Mage::helper ('pagarme')->getShippingAmount();
            $total = $baseSubtotalWithDiscount + $shippingAmount;

            $data = new Varien_Object();
            $data->setAmount(Mage::helper('pagarme')->formatAmount($address->getGrandTotal()))
                ->setInterestRate($interestRate)
                ->setMaxInstallments($numberInstallments)
                ->setFreeInstallments($freeInstallments);

            $post = Mage::app()->getRequest()->getPost();

            $installments = Mage::getModel('pagarme/api')->calculateInstallmentsAmount($data);
            $collection = $installments->getInstallments();

            if (!$collection) {
                return false;
            }

            $payment_installment = 0;
            if (isset ($post ['payment']['pagarme_checkout_installments'])) {
                $payment_installment = $post ['payment']['pagarme_checkout_installments'];
            }

            $this->prepareFeeAmount($collection,$payment_installment, $total, $quote, $address);
        }


        if ($payment == 'pagarme_cc') {

            $maxInstallments = (int) Mage::getStoreConfig('payment/pagarme_cc/max_installments');
            $minInstallmentValue = (float) Mage::getStoreConfig('payment/pagarme_cc/min_installment_value');
            $interestRate = (float) Mage::getStoreConfig('payment/pagarme_cc/interest_rate');
            $freeInstallments = (int) Mage::getStoreConfig('payment/pagarme_cc/free_installments');

            $baseSubtotalWithDiscount = Mage::helper('pagarme')->getBaseSubtotalWithDiscount();
            $shippingAmount = Mage::helper ('pagarme')->getShippingAmount();
            $total = $baseSubtotalWithDiscount + $shippingAmount;

            $numberInstallments = $this->getMaxInstallments($total,$minInstallmentValue, $maxInstallments);

            if (!$numberInstallments) {
                return $this;
            }

            $data = new Varien_Object();
            $data->setAmount(Mage::helper('pagarme')->formatAmount($total))
                ->setInterestRate($interestRate)
                ->setMaxInstallments($numberInstallments)
                ->setFreeInstallments($freeInstallments);

            $post = Mage::app()->getRequest()->getPost();

            $installments = Mage::getModel('pagarme/api')->calculateInstallmentsAmount($data);
            $collection = $installments->getInstallments();

            if (!$collection) {
                return false;
            }

            if (isset ($post ['payment']['installments'])) {
                $payment_installment = $post ['payment']['installments'];
            }

            $this->prepareFeeAmount($collection,$payment_installment, $total, $quote, $address);
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch (Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getFeeAmount();
        if (!$amount) return $this;

        $address->addTotal (array(
            'code' => $this->getCode (),
            'title' => Mage::helper ('pagarme')->__('Fee'),
            'value'=> $amount,
        ));

        return $this;
    }

    /**
     * @param $collection
     * @param $payment_installment
     * @param $total
     * @param $quote
     * @param $address
     */
    private function prepareFeeAmount($collection,$payment_installment, $total, $quote, $address)
    {
        foreach ($collection as $item) {

            if ($item->getInstallment() != $payment_installment) {
                continue;
            }

            $itemAmount = $item->getAmount() / 100;
            $itemAmount = number_format($itemAmount, 2, '.', '');

            $fee = $itemAmount - $address->getGrandTotal();

            $address->setFeeAmount($fee);
            $address->setBaseFeeAmount($fee);

            $quote->setFeeAmount($fee);

            $address->setGrandTotal($address->getGrandTotal() + $address->getFeeAmount());
            $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseFeeAmount());
        }
    }

    /**
     * @param $total
     * @param $minInstallmentValue
     * @param $maxInstallments
     * @return float|int
     */
    private function getMaxInstallments($total,$minInstallmentValue, $maxInstallments)
    {
        if (!$total) {
            return 1;
        }

        $numberInstallments = floor ($total / $minInstallmentValue);

        if ($numberInstallments > $maxInstallments) {
            return $maxInstallments;
        }

        return 1;
    }

}


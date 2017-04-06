<?php

class PagarMe_Core_Model_Quote_Address_Total
 extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    const LABEL_INTEREST_FEE = 'Interest fee';

    const LABEL_DISCOUNT = 'Discount';

    private $interestAmount;

    private $label;

    public function __construct()
    {
        $this->setCode('pagarme_checkout');
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return Mage::helper('pagarme_checkout')->__($this->label);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return PagarMe_Checkout_Model_Total
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);

        if (!$this->shouldCollect()) {
            return $this;
        }

        $quote = $address->getQuote();
        $subtotalAmount = $this->getSubtotal($quote);

        $transaction = $this->getTransaction();
        $totalAmount = Mage::helper('pagarme_core')
            ->parseAmountToFloat($transaction->getAmount());

        if ($transaction instanceof \PagarMe\Sdk\Transaction\BoletoTransaction) {
            $this->label = Mage::helper('pagarme_core')->__(self::LABEL_DISCOUNT);
        }

        if ($transaction instanceof \PagarMe\Sdk\Transaction\CreditCardTransaction) {
            $this->label = Mage::helper('pagarme_core')->__(self::LABEL_INTEREST_FEE);
        }

        $this->difference = $totalAmount - $subtotalAmount;

        $this->_setAmount($this->difference);
        $this->_setBaseAmount($this->difference);

        return $this;
    }

    /**
     * Add giftcard totals information to address object
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $addressTotalAmount = $address->getTotalAmount($this->getCode());

        if ($this->difference != 0 && $addressTotalAmount == 0) {
            $address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $this->getLabel(),
                'value' => $this->difference
            ));
        }

        return $this;
    }

    /**
     * @return bool
     */
    private function shouldCollect()
    {
        $paymentData = Mage::app()
            ->getRequest()
            ->getPost('payment');

        if (is_null($paymentData)) {
            return false;
        }

        if (!isset($paymentData['pagarme_checkout_token'])) {
            return false;
        }

        if ($this->interestAmount != 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $quote Mage_Sales_Model_Quote
     * @return double
     */
    private function getSubtotal($quote)
    {
        $quoteTotals = $quote->getTotals();
        $baseSubtotalWithDiscount = $quoteTotals['subtotal']->getValue();

        $shippingAmount = $quote->getShippingAddress()->getShippingAmount();

        return $baseSubtotalWithDiscount + $shippingAmount;
    }

    /**
     * @param $token string
     * @return PagarMe\Sdk\PagarMe\AbstractTransaction
     */
    private function getTransaction($token)
    {
        $paymentData = Mage::app()
            ->getRequest()
            ->getPost('payment');

        return Mage::getModel(
            'pagarme_core/sdk_adapter'
        )->getPagarMeSdk()
        ->transaction()
        ->get($paymentData['pagarme_checkout_token']);
    }
}

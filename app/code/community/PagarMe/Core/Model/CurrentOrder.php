<?php

class PagarMe_Core_Model_CurrentOrder
{

    private $quote;
    private $pagarMeSdk;

    public function __construct($quote, $pagarMeSdk)
    {
        $this->quote = $quote;
        $this->pagarMeSdk = $pagarMeSdk;
    }
    public function calculateInstallments(
        $maxInstallments,
        $freeInstallments,
        $interestRate
    ){
        $amount = $this->productsTotalValueInCents();
        Mage::log($interestRate);
        return $this->pagarMeSdk->getPagarMeSdk()
            ->calculation()
            ->calculateInstallmentsAmount(
                $amount,
                $interestRate,
                $freeInstallments,
                $maxInstallments
            );
    }

    //Subtotal should be the sum of all items in the cart
    //there's also Basesubtotal = subtotal in the store's currency
    //Pode levar à demora de mostrar os métodos de pagamento
    public function productsTotalValueInCents()
    {
        $total = $this->quote->getTotals()['subtotal']->getValue();
        return Mage::helper('pagarme_core')->parseAmountToInteger($total);
    }

    public function productsTotalValueInReals()
    {
        $total = $this->productsTotalValueInCents();
        return Mage::helper('pagarme_core')->parseAmountToFloat($total);
    }

    public function rateAmountInReals($installmentsValue, $freeInstallments, $interestRate)
    {
        $installments = $this->calculateInstallments(
            $installmentsValue,
            $freeInstallments,
            $interestRate
        );

        $installmentTotal = $installments[$installmentsValue]['total_amount'];
        return Mage::helper('pagarme_core')->parseAmountToFloat(
            $installmentTotal - $this->productsTotalValueInCents());
    }
}

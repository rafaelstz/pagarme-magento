<?php

class PagarMe_CreditCard_Model_CurrentOrder
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
}

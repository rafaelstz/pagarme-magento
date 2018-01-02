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
                $amount ?: 0,
                $interestRate ?: 0,
                $freeInstallments ?: 0,
                $maxInstallments ?: 1
            );
    }

    //Subtotal should be the sum of all items in the cart
    //there's also Basesubtotal = subtotal in the store's currency
    //Pode levar à demora de mostrar os métodos de pagamento
    public function productsTotalValueInCents()
    {
        $subtotalPunctuated = $this->quote->getData()['subtotal'];
        return preg_replace('/[^0-9]/', '', $subtotalPunctuated);
    }
}

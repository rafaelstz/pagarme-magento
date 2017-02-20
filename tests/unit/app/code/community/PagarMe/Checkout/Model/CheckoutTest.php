<?php

class PagarMe_Checkout_Model_CheckoutTest extends PHPUnit_Framework_TestCase
{
    protected $checkoutModel;

    public function setUp()
    {
        $this->checkoutModel = Mage::getModel('pagarme_checkout/checkout');
    }

    /**
     * @test
     */
    public function mustCreateBoletoTransaction()
    {
        $transactionHandlerMock = $this->getMockBuilder('PagarMe\\Sdk\\Transaction\\TransactionHandler')
            ->getMock();

        $pagarMeMock = $this->getMockBuilder('PagarMe\\Sdk\\PagarMe')
            ->getMock();

        $pagarMeMock->method('transaction')
            ->willReturn($transactionHandlerMock);

        $sdkMock = $this->getMockBuilder('PagarMe_Core_Model_Sdk_Adapter')
            ->getMock();

        $sdkMock->method('getPagarMeSdk')
            ->willReturn($sdkMock);

        $installments = 1;
        $cardhash = 'skdjafçsldjfasçlkdfjasldfjsdlkaf';
        $amount = 10.0;

        $this->checkoutModel
            ->assignData(
                [
                    'installments' => $installments,
                    'card_hash'    => $cardhash,
                ]
            );

        $payment = $this->getMockBuilder('Varien_Object')
            ->getMock();

        $payment->method('getAmount')
            ->willReturn($amount);

        $transactionHandlerMock->expects($this->once())
            ->method('boletoTransaction')
            ->with(
                [
                    $this->equalTo($amount),
                    $this->anything(),
                    $this->anything()
                ]
            );

        //$this->checkoutModel->authorize($payment);
    }
}

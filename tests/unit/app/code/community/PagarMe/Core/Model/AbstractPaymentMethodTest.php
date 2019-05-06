<?php

class PagarMe_Core_Model_AbstractPaymentMethodTest extends \PHPUnit_Framework_TestCase
{
    private $paymentMethod;

    public function setup()
    {
        $this->paymentMethod = $this
            ->getMockBuilder('PagarMe_Core_Model_AbstractPaymentMethod')
            ->setMethods([
                'isTransparentCheckoutActiveStoreConfig',
                'getActiveTransparentPaymentMethod',
                'getPostbackCode'
            ])
            ->getMock();

        $this->paymentMethod
            ->expects($this->any())
            ->method('getPostbackCode')
            ->willReturn('');
    }

    /**
     * @param boolean $status
     */
    private function setupTransparentCheckout($status)
    {
        $this->paymentMethod
            ->expects($this->any())
            ->method('isTransparentCheckoutActiveStoreConfig')
            ->willReturn($status);
    }

    private function setPaymentMethodActive($paymentMethod)
    {
        $this->paymentMethod
            ->expects($this->any())
            ->method('getActiveTransparentPaymentMethod')
            ->willReturn($paymentMethod);
    }

    /**
     * @test
     */
    public function mustReturnFalseWhenCheckoutTransparentIsInactive()
    {
        $this->assertFalse($this->paymentMethod->isAvailable());
    }

    /**
     * @test
     */
    public function mustReturnFalseIfCheckoutTransparentIsInactive()
    {
        $this->setupTransparentCheckout(false);

        $this->paymentMethod->_code = 'pagarme_creditcard';
        $this->assertFalse($this->paymentMethod->isAvailable());

        $this->paymentMethod->_code = 'pagarme_boleto';
        $this->assertFalse($this->paymentMethod->isAvailable());
    }

    /**
     * @test
     */
    public function mustReturnTrueOnlyForCreditCard()
    {
        $this->setupTransparentCheckout(true);
        $this->setPaymentMethodActive('pagarme_creditcard');

        $this->paymentMethod->_code = 'pagarme_creditcard';
        $this->assertTrue($this->paymentMethod->isAvailable());

        $this->paymentMethod->_code = 'pagarme_boleto';
        $this->assertFalse($this->paymentMethod->isAvailable());
    }
}
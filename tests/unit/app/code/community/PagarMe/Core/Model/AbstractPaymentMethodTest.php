<?php

class PagarMe_Core_Model_AbstractPaymentMethodTest extends \PHPUnit_Framework_TestCase
{
    private $paymentMethod;

    /**
     * @param boolean $status
     */
    private function setupTransparentCheckout($status)
    {
        $value = $status === true ? 1 : 0;

        Mage::getModel('core/config')
            ->saveConfig('payment/pagarme_configurations/transparent_active', $value);

        Mage::getModel('core/config')->cleanCache();
    }

    private function setPaymentMethodActive($paymentMethod)
    {
        Mage::getModel('core/config')
            ->saveConfig(
                'payment/pagarme_configurations/transparent_payment_methods',
                $paymentMethod
            );

        Mage::getModel('core/config')->cleanCache();
    }

    public function setUp()
    {
        $this->paymentMethod = $this
            ->getMockBuilder('PagarMe_Core_Model_AbstractPaymentMethod')
            ->setMethods([
                'getPostbackCode'
            ])
            ->getMock();

        $this->paymentMethod
            ->expects($this->any())
            ->method('getPostbackCode')
            ->willReturn('');

        $this->setupTransparentCheckout(true);
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

    public function tearDown()
    {
        $this->setupTransparentCheckout(false);
    }
}
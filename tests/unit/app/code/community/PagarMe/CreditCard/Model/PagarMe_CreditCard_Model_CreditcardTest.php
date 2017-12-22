<?php
/**
 * @author LeonamDias <leonam.pd@gmail.com>
 * @package PHP
 */


class PagarMeCreditCardModelCreditcardTest extends PHPUnit_Framework_TestCase
{
    private $creditCardModel;

    public function setUp()
    {
        $this->creditCardModel = $this->getMockBuilder(
            'PagarMe_CreditCard_Model_Creditcard'
        )->setMethods(['getMaxInstallments'])
         ->getMock();

        $this->creditCardModel
            ->method('getMaxInstallments')
            ->willReturn(6);
    }

    /**
     * @test
     */
    public function mustBeAnInstanceOfPagarmeCreditCardModel()
    {
        $this->assertInstanceOf(
            'PagarMe_CreditCard_Model_Creditcard',
            $this->creditCardModel
        );
    }

    public function installments()
    {
        return [
            [0, false],
            [5, true],
            [7, false],
            [13, false]
        ];
    }
    /**
     * @param int $installments
     * @param bool $shouldReturn
     *
     * @test
     * @dataProvider installments
     */
    public function installmentsMustBeInAValidRange($installments, $shouldReturn)
    {
        $this->assertEquals(
            $shouldReturn,
            $this->creditCardModel->isInstallmentsValid($installments)
        );
    }
}

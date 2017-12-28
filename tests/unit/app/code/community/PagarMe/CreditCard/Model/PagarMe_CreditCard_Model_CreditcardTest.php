<?php
/**
 * @author LeonamDias <leonam.pd@gmail.com>
 * @package PHP
 */

use PagarMe\Sdk\Card\Card;

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

    /**
     * Returns installments with expected return from isInstallmentsValid
     * method
     *
     * @return array
     */
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
    public function installmentsMustBeInAValidRange(
        $installments,
        $shouldReturn
    )
    {
        $this->assertEquals(
            $shouldReturn,
            $this->creditCardModel->isInstallmentsValid($installments)
        );
    }

    public function getSdkMock($cardHash = '')
    {
        $sdkMock = $this->getMockBuilder('\PagarMe\Sdk\PagarMe')
                        ->setMethods(['card', 'createFromHash'])
                        ->getMock();

        $sdkMock->expects($this->any())
                ->method('card')
                ->willReturnSelf();

        $sdkMock->expects($this->any())
                ->method('createFromHash')
                ->with($cardHash)
                ->willReturn(new Card([]));

        return $sdkMock;
    }

    /**
     * @test
     */
    public function mustReturnACardInstance()
    {
        $cardHash = 'test_transaction_e8Ij0oYalvjTEO17IHqKxNQcigKrYj';
        $sdk = $this->getSdkMock($cardHash);

        $creditCardModel = Mage::getModel('pagarme_creditcard/creditcard');
        $creditCardModel->setSdk($sdk);

        $card = $creditCardModel->generateCard($cardHash);

        $this->assertInstanceOf('\PagarMe\Sdk\Card\Card', $card);
    }

    /**
     * @test
     *
     * @expectedException PagarMe_CreditCard_Model_Exception_GenerateCard
     */
    public function mustThrowAGenerateCardExceptionInCaseOfErrors()
    {
        $cardHash = '';
        $sdk = $this->getSdkMock($cardHash);

        $sdk->expects($this->any())
            ->method('createFromHash')
            ->with($cardHash)
            ->will(
                $this->onConsecutiveCalls(
                    $this->throwException(new \PagarMe\Sdk\ClientException()),
                    $this->throwException(new \Exception())
                )
            );

        $creditCardModel = Mage::getModel('pagarme_creditcard/creditcard');
        $creditCardModel->setSdk($sdk);

        $creditCardModel->generateCard($cardHash);
        $creditCardModel->generateCard($cardHash);
    }
}

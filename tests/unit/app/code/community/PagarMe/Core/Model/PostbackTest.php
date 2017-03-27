<?php

class PagarMe_Core_Model_PostbackTest extends \PHPUnit_Framework_TestCase
{
    public function invalidPostbackData()
    {
        return [
            [
                true,
                '1515613216',
                'authorized'
            ],
            [
                false,
                '1515611234',
                'waiting_payment'
            ],
            [
                true,
                '1515234516',
                'payment_refund'
            ],
            [
                true,
                '1515612346',
                'refused'
            ]
        ];
    }

    public function mustProceedWithPostbackDataProvider()
    {
        return [
            [
                true,
                true,
                PagarMe_Core_Model_Postback::POSTBACK_STATUS_PAID
            ],
            [
                true,
                false,
                PagarMe_Core_Model_Postback::POSTBACK_STATUS_REFUNDED
            ],
            [
                false,
                true,
                'any'
            ],
            [
                false,
                false,
                PagarMe_Core_Model_Postback::POSTBACK_STATUS_PAID
            ]
        ];
    }

    public function mustProcessPostbackDataProvider()
    {
        return [
            [
                true,
                PagarMe_Core_Model_Postback::POSTBACK_STATUS_PAID,
                Mage_Sales_Model_Order::STATE_PROCESSING
            ],
            [
                false,
                PagarMe_Core_Model_Postback::POSTBACK_STATUS_REFUNDED,
                Mage_Sales_Model_Order::STATE_CLOSED
            ]
        ];
    }

    /**
     * @test
     * @dataProvider mustProceedWithPostbackDataProvider
     */
    public function mustProceedWithPostback(
        $expectedValue,
        $orderCanInvoice,
        $currentStatus
    ) {
        $postback = Mage::getModel('pagarme_core/postback');

        $orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->getMock();

        $orderMock->method('canInvoice')->willReturn($orderCanInvoice);

        $this->assertEquals(
            $expectedValue,
            $postback->canProceedWithPostback($orderMock, $currentStatus)
        );
    }

    /**
     * @test
     */
    public function mustSetOrderAsPaid()
    {
        $postback = Mage::getModel('pagarme_core/postback');

        $resourceMock = $this->getMockBuilder('Mage_Core_Model_Resource_Resource')
            ->getMock();

        $invoiceMock = $this->getMockBuilder('Mage_Sales_Model_Order_Invoice')
            ->getMock();

        $invoiceMock->method('getResource')
            ->willReturn($resourceMock);

        $invoiceMock->expects($this->once())
            ->method('register')
            ->willReturn($invoiceMock);

        $invoiceMock->expects($this->once())
            ->method('pay');

        $invoiceMock->expects($this->once())
            ->method('save');

        $invoiceServiceMock = $this->getMockBuilder('PagarMe_Core_Model_Service_Invoice')
            ->getMock();

        $postback->setInvoiceService($invoiceServiceMock);

        $invoiceServiceMock->expects($this->once())
            ->method('createInvoiceFromOrder')
            ->willReturn($invoiceMock);

        $transactionId = 120;

        $orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->getMock();

        $orderMock->method('getResource')
            ->willReturn($resourceMock);

        $orderMock->expects($this->once())
            ->method('setState')
            ->with(
                Mage_Sales_Model_Order::STATE_PROCESSING,
                true,
                'pago'
            );

        $orderMock->expects($this->once())
            ->method('save');

        $postback->setOrderAsPaid($orderMock);
    }

    /**
     * @test
     */
    public function mustSetOrderAsRefunded()
    {
        $resourceMock = $this->getMockBuilder('Mage_Core_Model_Resource_Resource')
            ->getMock();

        $invoiceMock = $this->getMockBuilder('Mage_Sales_Model_Order_Invoice')
            ->getMock();

        $invoiceMock->method('getResource')
            ->willReturn($resourceMock);

        $invoiceMock->expects($this->once())
            ->method('canRefund')
            ->willReturn(true);

        $orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->getMock();

        $orderMock->method('getResource')
            ->willReturn($resourceMock);

        $orderMock->expects($this->once())
            ->method('getInvoiceCollection')
            ->willReturn([
                $invoiceMock
            ]);

        $orderMock->expects($this->once())
            ->method('setState')
            ->with(
                Mage_Sales_Model_Order::STATE_CLOSED
            );

        $orderMock->expects($this->once())
            ->method('save');

        $postback = Mage::getModel('pagarme_core/postback');
        $postback->setOrderAsRefunded($orderMock);
    }

    /**
     * @test
     * @expectedException \Exception
     * @dataProvicer invalidPostbackData
     */
    public function mustThrowExceptionWhenCantProceedWithPostback($orderCanInvoice, $transactionId, $status)
    {
        $orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->getMock();

        $orderMock->method('canInvoice')
            ->willReturn($orderCanInvoice);

        $orderServiceMock = $this->getMockBuilder('PagarMe_Core_Model_Service_Order')
            ->getMock();

        $orderServiceMock->expects($this->once())
            ->method('getOrderByTransactionId')
            ->with($transactionId)
            ->willReturn($orderMock);

        $postback = Mage::getModel('pagarme_core/postback');
        $postback->setOrderService($orderServiceMock);
        $postback->processPostback($transactionId, "paid");
    }
}

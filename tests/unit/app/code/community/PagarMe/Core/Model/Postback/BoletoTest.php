<?php

class PagarMe_Core_Model_Postback_BoletoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function mustProcessPostback()
    {
        $resource = $this->getMockBuilder('Mage_Core_Model_Resource_Resource')
            ->getMock();

        $invoiceMock = $this->getMockBuilder('Mage_Sales_Model_Order_Invoice')
            ->getMock();

        $invoiceMock->expects($this->once())
            ->method('register')
            ->willReturn($invoiceMock);

        $invoiceMock->expects($this->once())
            ->method('pay');

        $invoiceMock->expects($this->once())
            ->method('save');

        $invoiceMock->method('getResource')
            ->willReturn($resource);

        $invoiceServiceMock = $this->getMockBuilder('PagarMe_Core_Model_Service_Invoice')
            ->getMock();

        $invoiceServiceMock->expects($this->once())
            ->method('createInvoiceFromOrder')
            ->willReturn($invoiceMock);

        $transactionId = 120;

        $orderMock = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->getMock();

        $orderMock->expects($this->once())
            ->method('setState')
            ->with(
                Mage_Sales_Model_Order::STATE_PROCESSING, true, "pago"
            );

        $orderMock->expects($this->once())
            ->method('save');

        $orderMock->method('getResource')
            ->willReturn($resource);

        $orderMock->method('canInvoice')
            ->willReturn(true);

        $orderServiceMock = $this->getMockBuilder('PagarMe_Core_Model_Service_Order')
            ->getMock();

        $orderServiceMock->expects($this->once())
            ->method('getOrderByTransactionId')
            ->with($transactionId)
            ->willReturn($orderMock);

        $orderServiceMock = $this->getMockBuilder('PagarMe_Core_Model_Service_Order')
            ->getMock();

        $orderServiceMock->expects($this->once())
            ->method('getOrderByTransactionId')
            ->with($transactionId)
            ->willReturn($orderMock);

        $postback = Mage::getModel('PagarMe_Core_Model_Postback_Boleto');
        $postback->setOrderService($orderServiceMock);
        $postback->setInvoiceService($invoiceServiceMock);
        $postback->processPostback($transactionId, "paid");
    }
}
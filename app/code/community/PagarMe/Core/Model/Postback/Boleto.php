<?php

class PagarMe_Core_Model_Postback_Boleto extends Mage_Core_Model_Abstract
{
    protected $invoiceService;

    private function canProceedWithPostback(Mage_Sales_Model_Order $order, $currentStatus)
    {
        return $order->canInvoice() && $currentStatus == "paid";
    }

    private function getOrderFromTransactionId($transactionId)
    {
        $orderId = Mage::getModel('pagarme_core/service_order')
            ->getOrderIdFromTransactionId($transactionId);

        return Mage::getModel('sales/order')->load($orderId);
    }

    public function getInvoiceService()
    {
        if(is_null($this->invoiceService)) {
            $this->invoiceService = Mage::getModel('pagarme_core/service_invoice');
        }

        return $this->invoiceService;
    }

    public function setInvoiceService(PagarMe_Core_Model_Service_Invoice $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function processPostback($transactionId, $currentStatus)
    {
        $order = $this->getOrderFromTransactionId($transactionId);

        if(!$this->canProceedWithPostback($order, $currentStatus)) {
            throw new Exception(
                Mage::helper('pagarme_core')->__('Can\'t proccess postback.')
            );
        }

        $invoice = $this->getInvoiceService()
            ->createInvoiceFromOrder($order);

        $invoice->register()
            ->pay();

        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, "pago");

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($order)
            ->addObject($invoice)
            ->save();

        return $order;
    }
}
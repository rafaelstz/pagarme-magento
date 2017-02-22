<?php

class PagarMe_Core_Model_Postback extends Mage_Core_Model_Abstract
{
    public function canProceedWithPostback($order, $currentStatus)
    {
        return $order->canInvoice() && $currentStatus == "paid";
    }

    public function processPostback($order, $invoice)
    {
        $invoice->prepareInvoice()
            ->register()
            ->pay();

        $order->setInProccess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($order)
            ->addObject($invoice)
            ->save();
    }
}
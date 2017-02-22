<?php

class PagarMe_Core_Model_Service_Invoice extends Mage_Core_Model_Abstract 
{
    public function createInvoiceFromOrder()
    {
        return Mage::getModel('sales/service_order', $order)
            ->prepareInvoice();
    }
}
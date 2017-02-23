<?php

class PagarMe_Core_Model_Service_Order
{
    public function getOrderByTransactionId($transactionId)
    {
        $orderId = Mage::getModel('pagarme_core/transaction')
            ->loadByTransactionId($transactionId)
            ->getOrderId();

        return Mage::getModel('sales/order')
            ->load($orderId);
    }
}
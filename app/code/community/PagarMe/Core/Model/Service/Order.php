<?php

class PagarMe_Core_Model_Service_Order
{
    public function getOrderByTransactionId($transactionId)
    {
        $transaction = Mage::getModel('pagarme_core/transaction')
            ->load($transactionId, 'transaction_id');

        $order = Mage::getModel('sales/order')
            ->load($transaction['order_id']);

        return $order;
    }
}
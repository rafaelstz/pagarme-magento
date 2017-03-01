<?php

class PagarMe_Core_Model_Service_Order
{
    /**
     * @param int $transactionId
     *
     * @return type
     */
    public function getOrderByTransactionId($transactionId)
    {
        $transaction = Mage::getModel('pagarme_core/transaction')
            ->load($transactionId, 'transaction_id');

        $order = Mage::getModel('sales/order')
            ->load($transaction['order_id']);

        return $order;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Mage_Sales_Model_Order
     *
     * @return int $transactionId
     */
    public function getTransactionIdByOrder(Mage_Sales_Model_Order $order)
    {
        return Mage::getModel('pagarme_core/transaction')
            ->load($order->getId(), 'order_id')
            ->getTransactionId();
    }
}

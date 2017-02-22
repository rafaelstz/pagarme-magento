<?php

class PagarMe_Core_Model_Service_Order
{
    private $orders = [
        1320888 => 33,
        1320890 => 34,
        1320896 => 35,
        1320903 => 36
    ];

    public function getOrderIdFromTransactionId($transactionId)
    {
        return $this->orders[$transactionId];
    }
}
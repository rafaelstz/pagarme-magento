<?php

namespace PagarMe\Magento\Test\Helper;

trait PostbackDataProvider
{
    public function getOrder($customer, $customerAddress, $products)
    {
        \Mage::app()
            ->setCurrentStore(1);

        $billlingAddress = $customer->getBillingAddress();
        $shippingAddress = $customer->getShippingAddress();

        $quote = \Mage::getModel('sales/quote')
            ->setStoreId(
                \Mage::app()->getStore()->getStoreId()
            )
            ->assignCustomer($customer);

        $quote->getBillingAddress()
            ->importCustomerAddress($customerAddress);

        $quote->getShippingAddress()
            ->importCustomerAddress($customerAddress);

        $quote->getShippingAddress()
            ->setCollectShippingRates(true);

        $quote->getPayment()->importData(
            [
                'method' => 'checkmo'
            ]
        );

        foreach ($products as $product) {
            $quote->addProduct($product, 1);
        }

        $quote->getShippingAddress()
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');

        $quote->collectTotals()->save();

        $service = \Mage::getModel('sales/service_quote', $quote);

        $service->submitAll();

        $order = $service->getOrder();

        return $order;
    }
}

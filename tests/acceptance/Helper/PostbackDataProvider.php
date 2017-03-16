<?php

namespace PagarMe\Magento\Test\Helper;

trait PostbackDataProvider
{
    public function getOrderPaidByBoleto($customer, $customerAddress, $products)
    {
        $quote = $this->createQuote($customer, $customerAddress, $products);

        $dataQuote = [
            'method' => 'pagarme_checkout',
            'pagarme_checkout_payment_method' => 'boleto',
            'pagarme_checkout_token' => $this->createToken('boleto', $quote->getGrandTotal())
        ];

        $quote->getPayment()->importData($dataQuote);
        $quote->save();

        $service = \Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        $order = $service->getOrder();

        return $order;
    }

    public function getOrderPaidByCreditCard($customer, $customerAddress, $products)
    {
        $quote = $this->createQuote($customer, $customerAddress, $products);

        $dataQuote = [
            'method' => 'pagarme_checkout',
            'pagarme_checkout_payment_method' => 'credit_card',
            'pagarme_checkout_token' => $this->createToken('credit_card', $quote->getGrandTotal())
        ];

        $quote->getPayment()->importData($dataQuote);
        $quote->collectTotals()->save();

        $service = \Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        $order = $service->getOrder();

        return $order;
    }

    private function createQuote($customer, $customerAddress, $products)
    {
        \Mage::app()
            ->setCurrentStore(1);

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

        foreach ($products as $product) {
            $quote->addProduct($product, 1);
        }

        $quote->getShippingAddress()
            ->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');

        $quote->collectTotals();

        return $quote;
    }

    public function createToken($paymentMethod, $amount)
    {
        $encryptionKey = \Mage::getStoreConfig(
            'payment/pagarme_settings/encryption_key'
        );

        $bodyData = [
            'amount' => '' . (int) ($amount * 100),
            'postback_url' => 'http://requestb.in/pkt7pgpk',
            'customer' => [
                'name' => 'Aardvark Silva',
                'email' => 'aardvark.silva@pagar.me',
                'document_number' => '18152564000105',
                'address' => [
                    'zipcode' => '01451001',
                    'neighborhood' => 'Jardim Paulistano',
                    'street' => 'Avenida Brigadeiro Faria Lima',
                    'street_number' => '1811'
                ],
                'phone' => [
                    'number' => '99999999',
                    'ddi' => '55',
                    'ddd' => '11'
                ]
            ],
            'metadata' => ['idProduto' => '13933139'],
            'capture' => 'false',
            'payment_method' => $paymentMethod,
            'encryption_key' => $encryptionKey
        ];

        if ($paymentMethod == 'credit_card') {
            $bodyData = array_merge(
                $bodyData,
                $this->getCreditCardData()
            );
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            'https://api.pagar.me/1/transactions',
            [
                'body' => $bodyData
            ]
        );

        $responseObj = json_decode((string) $response->getBody());
        return $responseObj->token;
    }

    private function getCreditCardData()
    {
        return [
            'card_number' => '4111111111111111',
            'card_holder_name' => 'Ricardo Ledo',
            'card_expiration_date' => '0220',
            'card_cvv' => '231'
        ];
    }

    private function createCommonDataForQuote($customer, $customerAddress)
    {
        $helper = \Mage::helper('pagarme_core');

        return [
            'method' => 'pagarme_checkout',
            'pagarme_checkout_customer_document_number' => $customer->getTaxvat(),
            'pagarme_checkout_customer_document_type' => 'cpf',
            'pagarme_checkout_customer_name' => $customer->getName(),
            'pagarme_checkout_customer_email' => $customer->getEmail(),
            'pagarme_checkout_customer_born_at' => $customer->getDob(),
            'pagarme_checkout_customer_phone_ddd' => $helper->getDddFromPhoneNumber($customerAddress->getTelephone()),
            'pagarme_checkout_customer_phone_number' => $helper->getPhoneWithoutDdd($customerAddress->getTelephone()),
            'pagarme_checkout_customer_address_street_1' => $customerAddress->getStreet(1),
            'pagarme_checkout_customer_address_street_2' => $customerAddress->getStreet(2),
            'pagarme_checkout_customer_address_street_3' => $customerAddress->getStreet(3),
            'pagarme_checkout_customer_address_street_4' => $customerAddress->getStreet(4),
            'pagarme_checkout_customer_address_city' => $customerAddress->getCity(),
            'pagarme_checkout_customer_address_state' => $customerAddress->getRegion(),
            'pagarme_checkout_customer_address_zipcode' => $customerAddress->getPostcode(),
            'pagarme_checkout_customer_address_country' => $customerAddress->getCountryId(),
            'pagarme_checkout_customer_gender' => $customer->getGender()
        ];
    }
}

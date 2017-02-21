<?php

class PagarMe_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function prepareCustomerData($data)
    {
        return (object) [
            'document_number' =>  Zend_Filter::filterStatic($data['pagarme_checkout_customer_document_number'], 'Digits'),
            'document_type'   => $data['pagarme_checkout_customer_document_type'],
            'name'            => $data['pagarme_checkout_customer_name'],
            'email'           => $data['pagarme_checkout_customer_email'],
            'born_at'         => $data['pagarme_checkout_customer_born_at'],
            'addresses'       => [
                (object) [
                    'street'        => $data['pagarme_checkout_customer_address_street_1'],
                    'street_number' => $data['pagarme_checkout_customer_address_street_2'],
                    'complementary' => $data['pagarme_checkout_customer_address_street_3'],
                    'neighborhood'  => $data['pagarme_checkout_customer_address_street_4'],
                    'city'          => $data['pagarme_checkout_customer_address_city'],
                    'state'         => $data['pagarme_checkout_customer_address_state'],
                    'zipcode'       => $data['pagarme_checkout_customer_address_zipcode'],
                    'country'       => $data['pagarme_checkout_customer_address_country'],
                ]
            ],
            'phones'          =>  [
                $this->preparePhoneNumber($data['pagarme_checkout_customer_phone'])
            ],
            'gender'          => $data['pagarme_checkout_customer_gender']
        ];
    }

    public function preparePhoneNumber($phoneNumber)
    {
        $filteredPhoneNumber = Zend_Filter::filterStatic($phoneNumber, 'Digits');

        $ddd = substr($filteredPhoneNumber, 0, 2);
        $number = substr($filteredPhoneNumber, 2);

        return (object) [
            'ddi' => null,
            'ddd' => $ddd,
            'number' => $number
        ];
    }
}
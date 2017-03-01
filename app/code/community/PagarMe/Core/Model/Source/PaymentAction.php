<?php

class PagarMe_Core_Model_Source_PaymentAction
{
    /**
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => Mage::helper('pagarme_core')->__('Authorize Only')
            ],
            [
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('pagarme_core')->__('Authorize and Capture')
            ],
        ];
    }
}

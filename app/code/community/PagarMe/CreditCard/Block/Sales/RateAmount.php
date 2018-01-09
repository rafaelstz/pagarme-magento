<?php

class PagarMe_CreditCard_Block_Sales_RateAmount extends Mage_Core_Block_Abstract
{
    /**
     * @return $this
     */
    public function initTotals()
    {
        $total = new Varien_Object([
            'code' => 'pagarme_modal_rate_amount',
            'field' => 'pagarme_modal_rate_amount',
            'value' => $rateAmount,
            'label' => 'Interest Fee',
        ]);

        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}

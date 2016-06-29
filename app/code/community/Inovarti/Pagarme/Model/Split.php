<?php

class Inovarti_Pagarme_Model_Split extends Mage_Payment_Model_Method_Abstract
{

    private $carrierAmount;
    private $carrierSplitAmount;
    private $recipientCarriers;

    /**
     * @param $quote
     * @return $this|array
     */
    public function prepareSplit($quote)
    {
        if (!Mage::getStoreConfig('payment/pagarme_settings/marketplace_is_active')) {
            return $this;
        }

        $marketplaceRecipientId = Mage::getStoreConfig('payment/pagarme_settings/marketplace_recipient_id');

        $this->carrierAmount = $quote->getShippingAddress()->getShippingInclTax();

        $checkSplitItems = Mage::getModel('sales/quote_item')
            ->getCollection()
            ->addFieldToFilter('quote_id', $quote->getId())
            ->addFieldToFilter('recipient_id', array('notnull' => true));

        if (!$checkSplitItems->getData()) {
            return $this;
        }

        $splitRulesData = $this->prepareSplitOrder($quote->getItemsCollection(), $marketplaceRecipientId);
        $splitRules = array();

        foreach ($splitRulesData['split_rules'] as $recipientId => $splitRulesValues) {

            foreach ($splitRulesValues as $splitRule) {

                $recipientRule = $splitRulesData['recipent_rules'][$recipientId];

              //  if ($recipientRule->getTypeAmountCharged() === 'variable') {

                    $recipientValue = $this->calculatePercetage($recipientRule->getAmount(), $splitRule['amount']);

                    if (isset($splitRules[$recipientId])) {

                        $lastedSplitData =  $splitRules[$recipientId];
                        $currentAmount = $splitRule['amount']-$recipientValue;

                        $splitRules[$recipientId] = [
                            'seller' => $lastedSplitData['seller'] + $recipientValue,
                            'fee_marketplace' => $lastedSplitData['fee_marketplace'] + $currentAmount,
                            'charge_processing_fee' => $recipientRule->getChargeProcessingFee(),
                            'liable' => $recipientRule->getLiable()
                        ];
                        continue;
                    }

                    $splitRules[$recipientId] = [
                        'seller' => $recipientValue,
                        'fee_marketplace' => $splitRule['amount']-$recipientValue
                    ];
             //   }
            }
        }

        $splitRule = array();
        $splitRuleMarketplace = array();

        foreach ($splitRules as $recipientId => $splitData) {

            $splitAmount = $this->getAmount($recipientId, $splitData['seller']);

            if ($splitAmount) {

                $splitRule[] = array(
                    'recipient_id' => $recipientId,
                    'charge_processing_fee' => $splitData['charge_processing_fee'],
                    'liable' => $splitData['liable'],
                    'amount' => Mage::helper('pagarme')->formatAmount($splitAmount)
                );
            }

            if ($splitRuleMarketplace[$marketplaceRecipientId]) {
                $currentAmount = $splitRuleMarketplace[$marketplaceRecipientId];
                $splitRuleMarketplace[$marketplaceRecipientId]['amount'] = $currentAmount['amount'] + $splitData['fee_marketplace'];
                continue;
            }

            if (count($this->recipientCarriers) === 1 && !in_array($recipientId,$this->recipientCarriers)) {
                $marketplaceAmount = $splitData['fee_marketplace'] + $this->carrierSplitAmount;
            } else {
                $marketplaceAmount = $splitData['fee_marketplace'];
            }

            $splitRuleMarketplace[$marketplaceRecipientId] = array(
                'recipient_id' => $marketplaceRecipientId,
                'charge_processing_fee' => $splitData['charge_processing_fee'],
                'liable' => $splitData['liable'],
                'amount' => $marketplaceAmount
            );
        }

        $splitRuleMarketplace[$marketplaceRecipientId]['amount'] = Mage::helper('pagarme')->formatAmount($splitRuleMarketplace[$marketplaceRecipientId]['amount']);
        $splitRule[] = $splitRuleMarketplace[$marketplaceRecipientId];

        return $splitRule;
    }

    /**
     * @param $checkSplitItems
     * @return mixed
     */
    private function prepareSplitOrder($checkSplitItems, $marketplaceRecipientId)
    {
        $splitRules = array();
        $recipientRules = array();
        $recipientCarriers = array();

        foreach ($checkSplitItems as $item) {

            $recipientId = ($item->getRecipientId())? $item->getRecipientId() : $marketplaceRecipientId;

            if (!$recipientRules[$recipientId]) {

                $recipientRule = Mage::getModel('pagarme/splitRules')
                    ->getCollection()
                    ->addFieldToFilter('recipient_id', $item->getRecipientId())
                    ->getFirstItem();

                if ($recipientRule->getShippingCharge()) {
                    array_push($recipientCarriers, $item->getRecipientId());
                }

                $recipientRules[$recipientId] = $recipientRule;
            }

            if (isset($splitRules[$recipientId])) {

                $splitRules[$recipientId][] = [
                    'sku' => $item->getSku(),
                    'amount' => ($item->getPrice() * $item->getQty())
                ];
                continue;
            }

            $splitRules[$recipientId][] = [
                'sku' => $item->getSku(),
                'amount' => ($item->getPrice() * $item->getQty())
            ];
        }

        $this->recipientCarriers = $recipientCarriers;
        $this->carrierSplitAmount = $this->carrierAmount / count($recipientCarriers);

        return [
            'split_rules' => $splitRules,
            'recipent_rules' => $recipientRules
        ];
    }

    /**
     * @param $recipientId
     * @param $amount
     * @return mixed
     */
    private function getAmount($recipientId, $amount)
    {
        if (in_array($recipientId,$this->recipientCarriers)) {
            return $amount + $this->carrierSplitAmount;
        }

        return $amount;
    }

    /**
     * @param $percetage
     * @param $total
     * @return float
     */
    public function calculatePercetage($percetage, $total)
    {
        return ($percetage / 100) * $total;
    }
}
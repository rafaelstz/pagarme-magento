<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Block_Form_Cc extends Mage_Payment_Block_Form_Cc
{
	const MIN_INSTALLMENT_VALUE = 5;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pagarme/form/cc.phtml');
    }

    public function getInstallmentsAvailables(){
    	$maxInstallments = (int)Mage::getStoreConfig('payment/pagarme_cc/max_installments');
    	$minInstallmentValue = (float)Mage::getStoreConfig('payment/pagarme_cc/min_installment_value');
    	if ($minInstallmentValue < self::MIN_INSTALLMENT_VALUE) {
    		$minInstallmentValue = self::MIN_INSTALLMENT_VALUE;
    	}

    	$quote = Mage::helper('checkout')->getQuote();
    	$total = $quote->getGrandTotal();

    	$n = floor($total / $minInstallmentValue);
    	if ($n > $maxInstallments) {
    		$n = $maxInstallments;
    	} elseif ($n < 1) {
    		$n = 1;
    	}

    	$installments = array();
    	for ($i=1; $i <= $n; $i++) {
    		$price = round($total / $i, 2);
    		if ($i==1) {
    			$label = $this->__('Pay in full - %s', $quote->getStore()->formatPrice($price, false));
    		} else {
    			$label = $this->__('%sx - %s', $i, $quote->getStore()->formatPrice($price, false));
    		}
    		$installments[$i] = $label;
    	}
    	return $installments;
    }
}
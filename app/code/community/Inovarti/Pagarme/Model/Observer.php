<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Model_Observer
{
    public function addPagarmeJs(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $blockType = $block->getType();
        $targetBlock = 'checkout/onepage_payment';
        if ($blockType == $targetBlock && Mage::getStoreConfig('payment/pagarme_cc/active')) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            $preHtml = $block->getLayout()
                ->createBlock('core/template')
                ->setTemplate('pagarme/checkout/payment/js.phtml')
                ->toHtml();
            $transport->setHtml($preHtml . $html);
        }
    }
}

<?php



class PagarMe_Core_Model_Observer_Autoloader extends Varien_Event_Observer
{
    /**
     * @param Varien_Event_Observer $event
     *
     * @codeCoverageIgnore
     */
    public function registerSplAutoloader($event)
    {
        require_once Mage::getBaseDir() . '/vendor/autoload.php';
    }
}

<?php

class PagarMe_Core_Model_Observer_AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function mustRegisterSplAutoloader()
    {
        $observer = $this->getMockBuilder('Varien_Event_Observer')
            ->getMock();

        $this->assertFalse(class_exists('PagarMe\Sdk\PagarMe'));

        $autoloader = Mage::getModel('pagarme_core/observer_autoloader');
        $autoloader->registerSplAutoloader($observer);

        $this->assertTrue(class_exists('PagarMe\Sdk\PagarMe'));
    }
}

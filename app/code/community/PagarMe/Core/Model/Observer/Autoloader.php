<?php

class PagarMe_Core_Model_Observer_Autoloader extends Varien_Event_Observer
{
    /**
     * @param Varien_Event_Observer $event
     */
    public function registerSplAutoloader($event)
    {
        spl_autoload_register(function ($class) {
            if (preg_match("^PagarMe\\", $class)) {
                die($class);
            }
        });
    }
}

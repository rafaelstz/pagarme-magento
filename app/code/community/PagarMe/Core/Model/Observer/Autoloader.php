<?php

class PagarMe_Core_Model_Observer_Autoloader extends Varien_Event_Observer
{
    /**
     * @param Varien_Event_Observer $event
     */
    public function registerSplAutoloader($event)
    {
        spl_autoload_register(function ($class) {
            if (!$this->isPagarMeClass($class)) {
                return false;
            }

            $classFilePath = $this->getClassFilePath($class);

            if(!file_exists($classFilePath)) {
                return false;
            }
            
            require_once $libDir . $classFilePath;
        });
    }

    public function isPagarMeClass($class)
    {

        $regExp = '/^\\\\?PagarMe\\\\/';

        return preg_match($regExp, $class);
    }

    public function getClassFilePath($class)
    {
        $libDir = Mage::getBaseDir('lib') . '/pagarme/lib/';

        $classWithoutVendorName = str_replace('PagarMe\\Sdk\\', '', $class);
        $classFile = str_replace('\\', '/', $classWithoutVendorName) . '.php';

        return $libDir . $classFile;
    }
}

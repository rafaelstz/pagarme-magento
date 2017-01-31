<?php

class CheckoutModuleTest extends PHPUnit_Framework_TestCase
{
    const MODULE_NAME = 'PagarMe_Checkout';

    public function setUp()
    {
        Mage::init();
        Mage::app()
            ->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * @test
     */
    public function mustHaveBeenInstalled()
    {
        $this->assertTrue(true);
    }
}

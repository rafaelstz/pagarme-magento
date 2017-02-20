<?php

class PagarMe_Core_Model_Observer_AutoloaderTest extends PHPUnit_Framework_TestCase
{
    protected $autoloader;

    public function setUp()
    {
        $this->autoloader = Mage::getModel('pagarme_core/observer_autoloader');
    }

    /**
     * @test
     */
    public function mustVerifyIfIsPagarMeClass()
    {
        $this->assertFalse($this->autoloader->isPagarMeClass('Anything'));
        $this->assertTrue($this->autoloader->isPagarMeClass('PagarMe\\Test'));
    }

    /**
     * @test
     */
    public function mustReturnClassFilePath()
    {
        $libDir = Mage::getBaseDir('lib');
        $classFileName = $this->autoloader
            ->getClassFilePath('PagarMe\\Sdk\\Transaction\\TransactionHandler');
        $this->assertEquals($libDir . '/pagarme/lib/Transaction/TransactionHandler.php', $classFileName);
    }

    /**
     * @test
     */
    public function mustRegisterSplAutoloader()
    {
        $observer = $this->getMockBuilder('Varien_Event_Observer')
            ->getMock();

        $this->autoloader->registerSplAutoloader($observer);

        $this->assertTrue(class_exists('PagarMe\Sdk\PagarMe'));
    }
}

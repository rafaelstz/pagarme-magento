<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class CheckoutContext extends MinkContext
{
    private $customer;

    private $address;

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        Mage::init();
        Mage::app()
            ->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    }

    /**
     * @Given a registered user
     */
    public function aRegisteredUser()
    {
        $websiteId = Mage::app()
            ->getWebsite()
            ->getId();

        $store = Mage::app()
            ->getStore();

        $this->customer = Mage::getModel('customer/customer')
            ->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname('Lívia Nina')
            ->setLastname('Isabelle Freitas')
            ->setTaxvat('41.724.895-7')
            ->setDob('03/12/1980')
            ->setEmail('livia_nina@arganet.com.br')
            ->setPassword('q6Cyxg4TMM');

        $this->customer->save();

        $this->address = Mage::getModel('customer/address')
            ->setData(
                array(
                    'firstname'  => 'Lívia Nina',
                    'lastname'   => 'Isabelle Freitas',
                    'street'     => array(
                        '0' => 'Rua Siqueira Campos',
                        '1' => '515',
                        '2' => '',
                        '3' => 'Jacintinho'
                    ),
                    'city'       => 'Maceió',
                    'region_id'  => '',
                    'region'     => 'SP',
                    'postcode'   => '57040460',
                    'country_id' => 'BR',
                    'telephone'  => '(82) 99672-3631'
                )
            )
            ->setCustomerId($this->customer->getId())
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');

        $this->address->save();
    }

    /**
     * @Given a valid credit card
     */
    public function aValidCreditCard()
    {
        $this->creditCard = array(
            'number' => '4111111111111111',
            'cvv'    => '123'
        );
    }

    /**
     * @When I access the store page
     */
    public function iAccessTheStorePage()
    {
        $session = $this->getSession();
        $session->visit('http://magento/');
    }

    /**
     * @When add any product to basket
     */
    public function addAnyProductToBasket()
    {
        throw new PendingException();
    }

    /**
     * @When i go to checkout page
     */
    public function iGoToCheckoutPage()
    {
        throw new PendingException();
    }

    /**
     * @When I use a valid credit card to pay
     */
    public function iUseAValidCreditCardToPay()
    {
        throw new PendingException();
    }

    /**
     * @Then the purchase must be paid with success
     */
    public function thePurchaseMustBePaidWithSuccess()
    {
        throw new PendingException();
    }

    /**
     * @AfterScenario
     */
    public function tearDown()
    {
        //$this->customer->delete();
    }
}

<?php

use Behat\MinkExtension\Context\RawMinkContext;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class OneStepCheckoutContext extends RawMinkContext
{
    use PagarMe\Magento\Test\Helper\AdminAccessProvider;
    use PagarMe\Magento\Test\Helper\AdminDataProvider;
    use PagarMe\Magento\Test\Helper\CustomerDataProvider;
    use PagarMe\Magento\Test\Helper\PagarMeSettings;
    use PagarMe\Magento\Test\Helper\ProductDataProvider;

    protected $adminUser;

    const INOVARTI_CHECKOUT_ON = 1;
    const INOVARTI_CHECKOUT_OFF = 0;

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        $this->adminUser = $this->createAdminUser();
        $this->loginOnAdmin($this->adminUser);
        $this->goToSystemSettings();
        $this->enableInovartiOneStepCheckout();

        $this->customer = $this->getCustomer();
        $this->customer->save();

        $this->customerAddress = $this->getCustomerAddress();
        $this->customerAddress->setCustomerId($this->customer->getId());
        $this->customerAddress->save();

        $this->product = $this->getProduct();
        $this->product->save();

        $stock = $this->getProductStock();
        $stock->assignProduct($this->product);
        $stock->save();
    }

    /**
     * @Given i Am on checkout page using Inovarti One Step Checkout
     */
    public function iAmOnCheckoutPageUsingInovartiOneStepCheckout()
    {
        //configure Pagar.me
        //login user
        //add item to cart
        //go to checkout page
    }

    /**
     * @When I confirm payment
     */
    public function iConfirmPayment()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Then the purchase must be created with success
     */
    public function thePurchaseMustBeCreatedWithSuccess()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    /**
     * @Then a link to boleto must be provided
     */
    public function aLinkToBoletoMustBeProvided()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }

    private function enableInovartiOneStepCheckout()
    {
        $this->switchInovartiOneStepCheckout(self::INOVARTI_CHECKOUT_ON);
    }

    private function disableInovartiOneStepCheckout()
    {
        $this->switchInovartiOneStepCheckout(self::INOVARTI_CHECKOUT_OFF);
    }

    private function switchInovartiOneStepCheckout($option)
    {
        $page = $this->getSession()->getPage();

        $page->find(
                'named',
                array(
                    'link',
                    'One Step Checkout'
                )
            )->click();

        $inovartiSettingsHeader = $page->find(
            'css',
            '#onestepcheckout_general-head'
        );

        if (!$inovartiSettingsHeader->hasClass('open')) {
            $inovartiSettingsHeader->click();
        }

        $page->find(
            'css',
            '#onestepcheckout_general_is_enabled'
            )->selectOption($option);

        $page->pressButton('Save Config');
    }

    /**
     * @AfterScenario
     */
    public function tearDown()
    {
        $this->disableInovartiOneStepCheckout();
    }
}

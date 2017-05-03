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
        $adminUser = $this->createAdminUser();

        $this->setupInovarti($adminUser);
        $this->setupCustomer();
        $this->setupProduct();
        $this->setupPagarMe($adminUser);
    }

    /**
     * @Given i Am on checkout page using Inovarti One Step Checkout
     */
    public function iAmOnCheckoutPageUsingInovartiOneStepCheckout()
    {
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

    private function setupCustomer()
    {
        $this->customer = $this->getCustomer();
        $this->customer->save();

        $this->customerAddress = $this->getCustomerAddress();
        $this->customerAddress->setCustomerId($this->customer->getId());
        $this->customerAddress->save();
    }

    private function setupProduct()
    {
        $this->product = $this->getProduct();
        $this->product->save();

        $stock = $this->getProductStock();
        $stock->assignProduct($this->product);
        $stock->save();
    }

    private function setupInovarti($adminUser)
    {
        $this->loginOnAdmin($adminUser);
        $this->goToSystemSettings();
        $this->enableInovartiOneStepCheckout();
    }

    private function setupPagarMe($adminUser)
    {
        $this->loginOnAdmin($adminUser);
        $this->goToSystemSettings();

        $session = $this->getSession();
        $page = $session->getPage();

        $page->find('named', array('link', 'Payment Methods'))->click();
        $page->find('css', '#payment_pagarme_settings-head')->click();

        $page->find(
            'named',
            array(
                'id',
                'payment_pagarme_settings_api_key'
            )
        )->setValue(PAGARME_API_KEY);

        $page->find(
            'named',
            array(
                'id',
                'payment_pagarme_settings_encryption_key'
            )
        )->setValue(PAGARME_ENCRYPTION_KEY);

        $page->pressButton('Save Config');
    }
}

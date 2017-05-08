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
    use PagarMe\Magento\Test\Helper\Interaction;
    use PagarMe\Magento\Test\Helper\Configuration\Inovarti;

    protected $pagarMeCheckout;
    protected $product;

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        $this->setupInovarti();
        $this->setupCustomer();
        $this->setupProduct();
        $this->setupPagarMe();
    }

    /**
     * @Given i Am on checkout page using Inovarti One Step Checkout
     */
    public function iAmOnCheckoutPageUsingInovartiOneStepCheckout()
    {
        $this->setupCart();
        $this->loginOnOneStepCheckout();
    }

    /**
     * @When I confirm payment
     */
    public function iConfirmPayment()
    {
        $page = $this->getSession()->getPage();

        $page->find('css', '#p_method_pagarme_checkout')->click();
        $this->getSession()->wait(5000);

        $button = $page->find('css', '#pagarme-checkout-fill-info-button');

        $page->find('css', '#pagarme-checkout-fill-info-button')->click();
        $this->getSession()->wait(5000);

        $this->getSession()->switchToIframe(
            $page->find('css', 'iframe')->getAttribute('name')
        );

        $this->pagarMeCheckout = $this->getSession()->getPage();
        $this->pagarMeCheckout->pressButton('Boleto');

        $this->waitForElement(
            '#pagarme-modal-box-step-buyer-information',
            1000
        );

        $this->pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-step-buyer-information .pagarme-modal-box-next-step'
        )->click();

        $this->waitForElement(
            '#pagarme-modal-box-step-customer-address-information',
            1000
        );

        $this->pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-step-customer-address-information .pagarme-modal-box-next-step'
        )->click();

        $page = $this->getSession()->wait(5000);

        $this->getSession()->switchToIframe();
        $page = $this->getSession()->getPage();
        $page->pressButton(Mage::helper('pagarme_checkout')->__('Place Order'));

        $this->getSession()->wait(5000);
    }

    /**
     * @Then the purchase must be created with success
     */
    public function thePurchaseMustBeCreatedWithSuccess()
    {
        $page = $this->getSession()->getPage();

        $successMessage = $page->find('css', 'h1')
            ->getText();

        \PHPUnit_Framework_TestCase::assertEquals(
            getenv('MAGENTO_URL') . 'index.php/checkout/onepage/success/',
            $this->getSession()->getCurrentUrl()
        );

        \PHPUnit_Framework_TestCase::assertEquals(
            strtolower(
                Mage::helper(
                    'pagarme_checkout'
                )->__('Your order has been received.')
            ),
            strtolower($successMessage)
        );
    }

    /**
     * @Then a link to boleto must be provided
     */
    public function aLinkToBoletoMustBeProvided()
    {
        $page = $this->getSession()->getPage();

        \PHPUnit_Framework_TestCase::assertContains(
            'Para imprimir o boleto',
            $page->find(
                'css',
                '.pagarme_info_boleto'
            )->getText()
        );

        \PHPUnit_Framework_TestCase::assertContains(
            'https://pagar.me',
            $page ->find(
                'css',
                '.pagarme_info_boleto a'
            )->getAttribute('href')
        );
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

    private function setupInovarti()
    {
        $this->enableInovartiOneStepCheckout();
    }

    private function setupPagarMe()
    {
        \Mage::getModel('core/config')->saveConfig('payment/pagarme_settings/active', 1);
        \Mage::getModel('core/config')->saveConfig(
            'payment/pagarme_settings/api_key',
            PAGARME_API_KEY
        );
        \Mage::getModel('core/config')->saveConfig(
            'payment/pagarme_settings/encryption_key',
            PAGARME_ENCRYPTION_KEY
        );
    }

    private function setupCart()
    {
        $session = $this->getSession();

        $session->visit(getenv('MAGENTO_URL'));
        $page = $session->getPage();

        $page->clickLink($this->product->getName());

        $page->pressButton(
            Mage::helper('pagarme_checkout')->__('Add to Cart')
        );

        $page->pressButton(
            Mage::helper('pagarme_checkout')->__('Proceed to Checkout')
        );
    }

    private function loginOnOneStepCheckout()
    {
        $page = $this->getSession()->getPage();

        $page->fillField(
            Mage::helper('pagarme_checkout')->__('Email Address'),
            $this->customer->getEmail()
        );

        $page->fillField(
            Mage::helper('pagarme_checkout')->__('Password'),
            $this->customer->getPassword()
        );

        $page->pressButton('Login');

        $this->getSession()->wait(2000);
    }
}

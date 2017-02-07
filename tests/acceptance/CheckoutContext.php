<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Tester\Exception\PendingException;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class CheckoutContext extends MinkContext
{
    private $customer;

    private $session;

    /**
     * @Given a registered user
     */
    public function aRegisteredUser()
    {
        $this->customer = FeatureContext::getCustomer();
    }

    /**
     * @Given any product
     */
    public function anyProduct()
    {
    }

    /**
     * @Given a valid credit card
     */
    public function aValidCreditCard()
    {
    }

    /**
     * @When I access the store page
     */
    public function iAccessTheStorePage()
    {
        $this->getSession()
            ->visit(getenv('MAGENTO_URL'));
    }

    /**
     * @When add any product to basket
     */
    public function addAnyProductToBasket()
    {
        $page = $this->getSession()
            ->getPage();

        $page->pressButton('Add to Cart');
    }

    /**
     * @When i go to checkout page
     */
    public function iGoToCheckoutPage()
    {
        $page = $this->getSession()
            ->getPage();

        $page->pressButton('Proceed to Checkout');
    }

    /**
     * @When login with registered user
     */
    public function loginWithRegisteredUser()
    {
        $page = $this->getSession()
            ->getPage();

        $inputEmail = $page->find('named', array('id', 'login-email'));
        $inputEmail->setValue($this->customer->getEmail());

        $inputPassword = $page->find('named', array('id', 'login-password'));
        $inputPassword->setValue($this->customer->getPassword());

        $page->pressButton('Login');
    }

    /**
     * @When confirm billing information
     */
    public function confirmBillingInformation()
    {
        $session = $this->getSession();
        $page = $session->getPage();
        $page->find('css', '#billing-buttons-container button')->press();
    }

    /**
     * @When select shipping method
     */
    public function selectShippingMethod()
    {
        $session = $this->getSession();
        $session->wait(
            5000,
            "document.querySelector('#checkout-step-shipping_method').style.display != 'none'"
        );
        $page = $session->getPage();
        $page->find('css', '#shipping-method-buttons-container button')->press();
    }

    /**
     * @When select payment method
     */
    public function selectPaymentMethod()
    {
        $session = $this->getSession();
        $session->wait(
            5000,
            "document.querySelector('#checkout-step-payment').style.display != 'none'"
        );

        $page = $session->getPage();
        $page->find(
            'named',
            array(
                'radio',
                'Pagarme Checkout'
            )
        )->check();

        $page->find(
            'css',
            '#payment-buttons-container button'
        )->press();
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
}

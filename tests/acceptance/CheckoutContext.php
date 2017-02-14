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
                'id',
                'p_method_pagarme_checkout'
            )
        )->click();

        $page->pressButton(Mage::helper('pagarme_checkout')->__('Fill in the card data'));

        $session->wait(
            1000,
            "document.querySelector('#pagarme-checkout-ui') != null"
        );    

    }

    /**
     * @When I use a valid credit card to pay
     */
    public function iUseAValidCreditCardToPay()
    {
        $session = $this->getSession();

        $page = $session->getPage();

        $session->switchToIframe($page->find('css' ,'iframe')->getAttribute('name'));

        $pagarMeCheckout = $session->getPage();
        
        $pagarMeCheckout->pressButton('Cartão de crédito');

        $session->wait(
            1000,
            "document.querySelector('#pagarme-modal-box-step-buyer-information').style.display != 'none'"
        );

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-buyer-name'
        )->setValue($this->customer->getName());

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-buyer-email'
        )->setValue($this->customer->getEmail());

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-buyer-document-number'
        )->setValue($this->customer->getTaxvat());

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-buyer-ddd'
        )->setValue('11');

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-buyer-number'
        )->setValue('995551668');

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-step-buyer-information .pagarme-modal-box-next-step'
        )->click();

        $session->wait(
            1000,
            "document.querySelector('#pagarme-modal-box-step-customer-address-information').style.display != 'none'"
        );

        $billingAddress = FeatureContext::getCustomerAddress();

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-zipcode'
        )->setValue($billingAddress->getPostcode());

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-street'
        )->setValue($billingAddress->getStreet()[0]);

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-number'
        )->setValue($billingAddress->getStreet()[1]);

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-complementary'
        )->setValue($billingAddress->getStreet()[2]);

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-neighborhood'
        )->setValue($billingAddress->getStreet()[3]);

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-city'
        )->setValue($billingAddress->getCity());

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-customer-address-state'
        )->setValue($billingAddress->getState());

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-step-customer-address-information .pagarme-modal-box-next-step'
        )->click();

        $session->wait(
            1000,
            "document.querySelector('#pagarme-modal-box-step-credit-card-information').style.display != 'none'"
        );

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-credit-card-number'
        )->setValue('4242424242424242');

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-credit-card-name'
        )->setValue($this->customer->getName());

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-credit-card-expiration'
        )->setValue('1020');

        $pagarMeCheckout->find(
            'css',
            '#pagarme-modal-box-credit-card-cvv'
        )->setValue('123');

        $pagarMeCheckout->find(
            'css', 
            '#pagarme-modal-box-step-credit-card-information .pagarme-modal-box-next-step'
        )->click();

        $session->switchToIframe();

        $session->wait(
            5000,
            "document.querySelector('#pagarme-checkout-container').style.display == 'none'"
        );
        
        $page->find(
            'css',
            '#payment-buttons-container button'
        )->press();

        $session->wait(
            2000,
            "document.querySelector('#checkout-step-review').style.display != 'none'"
        );

        $page->pressButton(Mage::helper('pagarme_checkout')->__('Place Order'));
    }

    /**
     * @Then the purchase must be paid with success
     */
    public function thePurchaseMustBePaidWithSuccess()
    {
        $session = $this->getSession();
        $session->wait(5000);

        $page = $session->getPage();

        $successMsg = $page->find('css', 'h1')
            ->getText();

        \PHPUnit_Framework_TestCase::assertEquals(getenv('MAGENTO_URL') . 'index.php/checkout/onepage/success/', $session->getCurrentUrl());
        \PHPUnit_Framework_TestCase::assertEquals(Mage::helper('pagarme_checkout')->__('Your order has been received.'), $successMsg);
    }
}

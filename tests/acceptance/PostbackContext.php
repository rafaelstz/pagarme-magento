<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Tester\Exception\PendingException;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class PostbackContext extends MinkContext
{

    use PagarMe\Magento\Test\Helper\CustomerDataProvider;
    use PagarMe\Magento\Test\Helper\ProductDataProvider;
    use PagarMe\Magento\Test\Helper\PostbackDataProvider;

    /**
     * @BeforeScenario
     */
    public function setUp()
    {
        $this->customer = $this->getCustomer();
        $this->customer->save();

        $this->customerAddress = $this->getCustomerAddress();
        $this->customerAddress->setCustomerId($this->customer->getId());
        $this->customerAddress->save();

        $this->product = $this->getProduct();
        $this->product->save();
    }

    /**
    * @Given a pending boleto order
    */
    public function aPendingBoletoOrder()
    {
        $this->order = $this->getOrder(
            $this->customer,
            $this->customerAddress,
            [
                $this->product
            ]
        );

        $this->order->save();
    }

    /**
    * @When I receive a postback with status :arg1
    */
    public function iReceiveAPostbackWithStatus($arg1)
    {
        throw new PendingException();
    }

    /**
    * @Then my order must be updated to :arg1
    */
    public function myOrderMustBeUpdatedTo($arg1)
    {
        throw new PendingException();
    }
}

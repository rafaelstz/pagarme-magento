<?php

namespace PagarMe\Magento\Test\CreditCard;

trait AdminInterestRateCheck
{
    /**
     * @When I login to the admin
     */
    public function iLoginToTheAdmin()
    {
        $this->session
            ->visit($this->magentoUrl . '/admin');

        $page = $this->session->getPage();
        $this->waitForElement('#username', 2000);
        $page->fillField(
            \Mage::helper('pagarme_modal')->__('Password'),
            'magentorocks1'
        );
        $page->fillField(
            \Mage::helper('pagarme_modal')->__('User Name'),
            'admin'
        );
        $page->pressButton('Login');
    }

    /**
     * @When I check the order interest amount in its admin detail page
     */
    public function iCheckTheOrderInterestAmountInItsAdminDetailPage()
    {
        $order = \Mage::getModel('sales/order')
            ->load($this->createdOrderId, 'increment_id');
        $this->session
            ->visit(
                $this->magentoUrl . 'admin/sales_order/view/order_id/' . $order->getId()
            );
    }

    /**
     * @Then the admin interest value should consider the values :installments and :interestRate
     */
    public function theAdminInterestValueShouldConsiderTheValuesAnd(
        $installments,
        $interestRate
    ) {
        $this->waitForElement('#pagarme_creditcard_order_info_rate_amount', 3000);
        $page = $this->session->getPage();
        $interestAmount = $page
            ->find('css', '#pagarme_creditcard_order_info_rate_amount td:last-of-type')
            ->getText();

        \PHPUnit_Framework_TestCase::assertEquals(
            $interestAmount,
            'R$11.22'
        );

    }
}

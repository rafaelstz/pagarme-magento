Feature: Credit Card
    As an administrator of a webstore
    I want to use the transparent checkout for credit card purchases with installments
    So that the clients on my store can buy their goods without knowing that the payment is resolved by Pagar.me
    Scenario: Make a purchase by credit card
        Given a registered user
        When I access the store page
        And add any product to basket
        And I go to checkout page
        And login with registered user
        And confirm billing and shipping address information
        And choose pay with transparent checkout using credit card
        And I confirm my payment information
        And place order
        Then the purchase must be paid with success

    Scenario Outline: Change the max installments configuration
        Given a registered user
        When I set max installments to "<max_installments>"
        And I access the store page
        And add any product to basket
        And I go to checkout page
        And login with registered user
        And confirm billing and shipping address information
        And choose pay with transparent checkout using credit card
        And I confirm my payment information
        And I should see only installment options up to "<max_installments>"
        And place order
        Then the purchase must be paid with success
        Examples:
        | max_installments  |
        | 12                |
        | 3                 |
        | 1                 |

    @only
    Scenario Outline: Make a purchase by credit card with interest and installments
        Given a registered user
        When I set max installments to "<max_installments>"
        And I set interest rate to "<interest_rate>"
        And I access the store page
        And add any product to basket
        And I go to checkout page
        And login with registered user
        And confirm billing and shipping address information
        And choose pay with transparent checkout using credit card
        And I choose "<max_installments>"
        And I confirm my payment information
        Then the purchase must be created with value based on both "<max_installments>" and "<interest_rate>"
        Examples:
        | max_installments | interest_rate |
        | 10               | 10            |

    @order_view_interest
    Scenario Outline: Check the interest in the order details page
        Given a registered user
        And a created order with installment value of "<installments>" and interest of "<interest_rate>"
        When I check the order interest amount in its detail page
        Then the interest value should consider the values "<installments>" and "<interest_rate>"
        Examples:
        | installments | interest_rate |
        | 10           | 10            |

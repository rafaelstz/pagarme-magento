Feature: One Step Checkout Pagar.me
    As a seller that use Magento
    I want to allow that purchases must be paid using Checkout Pagar.me
    And One Step Checkout
    To make sales

    Scenario: Make a purchase by boleto
        Given I am on checkout page using Inovarti One Step Checkout
        When I confirm payment
        Then the purchase must be created with success
        And a link to boleto must be provided

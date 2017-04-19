Feature: Order Visualization
    As an administrator of a webstore
    I want to visualize the Pagar.me transaction information
    So then I can check all the information on my transactions

    Scenario: Order Rate Value and Info from Credit Card Purchase
        Given a registered user
        When I access the store page
        And add any product to basket
        And I go to checkout page
        When login with registered user
        And confirm billing and shipping address information
        And choose pay with pagar me checkout using "Cartão de crédito"
        And I confirm my personal data
        Given a valid credit card
        And I confirm my payment information with 1 installments
        And finish payment process
        Then I see the customer payment information using "Cartão de Crédito"
        And I see the customer selected 1 installments
        And place order
        Then the purchase must be paid with success
        Then as an Admin user
        When I access the admin
        And navigate to the Order page
        And click on the last created Order
        Then I see that the interest rate information for "Cartão de Crédito" is present
        Then I, as a registered user, navigate to My Account
        And click on my Order
        Then I see my payment method selection as "Cartão de Crédito"
        And I see my installment selection as 1
        And I see my rate amount

    Scenario: Order Rate Value and Info from Boleto Purchase
        Given a registered user
        When I access the store page
        And add any product to basket
        And I go to checkout page
        When login with registered user
        And confirm billing and shipping address information
        And choose pay with pagar me checkout using "Boleto"
        And I confirm my personal data
        And finish payment process
        Then I see the customer payment information using "Boleto"
        And place order
        Then the purchase must be paid with success
        Then as an Admin user
        When I access the admin
        And navigate to the Order page
        And click on the last created Order
        Then I see that the interest rate information for "Boleto" is present
        Then I, as a registered user, navigate to My Account
        And click on my Order
        Then I see my payment method selection as "Boleto"


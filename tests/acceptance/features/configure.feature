Feature: Configuration Form
    As a administrator of webstore
    I want to change api key, encription key and the other configs
    So that I can customize module behavior

    Scenario: Inserting the API_KEY and EK_KEY
        Given a admin user
        And a api key
        And a encryption key
        When I access the admin
        And go to system configuration page
        And insert an API key
        And insert an encryption key
        And save configuration
        Then the configuration must be saved with success

    Scenario: Disable payment method
        Given a admin user
        When I access the admin
        And go to system configuration page
        And select payment method "Boleto Only"
        And save configuration
        And any customer try to buy any product
        Then the "Boleto" button must not be found

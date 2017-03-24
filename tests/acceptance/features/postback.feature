Feature: Update order status
    As a seller that use Magento
    I want receive updates about transactions status
    To keep order workflow updated and financial health of my store

    Scenario Outline: Receiving boleto order status update
        Given a pending boleto order
        When I receive a postback for boleto with status "<postback_status>"
        Then my order must be updated to "<new_status>"
        Examples:
        | postback_status | new_status |
        | paid            | processing |
        | refunded        | closed     |

    Scenario: Receiving credit card order status update
        Given a pending credit card order
        When I receive a postback for credit card with status "paid"
        Then my order must be updated to "processing"

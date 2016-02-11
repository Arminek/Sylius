@ui-checkout @insulated @mink:firefox @paypal
Feature: Checkout with PayPal Express Checkout
    In order to buy products
    As a Customer
    I want to be able to pay with PayPal Express Checkout

    Background:
        Given the store is operating on a single channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships to "France"
        And the store allows paying "PayPal Express Checkout"
        And the store ships everything for free
        And I am logged in as "john@example.com"

    Scenario: Being redirected to the PayPal Express Checkout page
        Given I added product "PHP T-Shirt" to the cart
        When I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order
        Then I should be redirected to PayPal Express Checkout page

    Scenario: Successful payment
        Given I added product "PHP T-Shirt" to the cart
        And I proceed selecting "PayPal Express Checkout" payment method
        And I confirm my order
        When I sign in to PayPal and pay successfully
        Then I should be redirected back to the thank you page

@ui
Feature: Checkout with PayPal Express Checkout
  In order to buy products
  As a Customer
  I want to be able to pay with PayPal Checkout Express

  Background:
    Given that store is operating on the "United States" channel
    And default currency is USD
    And there is user "john@example.com" identified by "password123"
    And catalog has a product "PHP T-Shirt" priced at $19.99
    And store allows paying with PayPal Checkout Express
    And I am logged in as "john@example.com"

  Scenario: Being redirected to the PayPal Express Checkout page
    Given I added product "PHP T-Shirt" to cart
    When I proceed selecting PayPal Checkout Express payment method
    And I confirm my order
    Then I should be redirected to PayPal Checkout Express page

  Scenario: Cancelling the payment
    Given I added product "PHP T-Shirt" to cart
    And I confirmed my order selecting PayPal Checkout Express payment method
    And I am on the PayPal Checkout Express page
    When I cancel my PayPal payment
    Then I should be redirected back to the order payment page
    And I should see one cancelled payment and new one ready to be paid

  Scenario: Successful payment
    Given I added product "PHP T-Shirt" to cart
    And I confirmed my order selecting PayPal Checkout Express payment method
    And I am on the PayPal Checkout Express page
    When I sign in to PayPal and pay successfully
    Then I should be redirected back to the thank you page

  Scenario: Payment failed
    Given I added product "PHP T-Shirt" to cart
    And I confirmed my order selecting PayPal Checkout Express payment method
    And I am on the PayPal Checkout Express page
    When I sign in to PayPal but fail to pay
    Then I should be redirected back to the order payment page
    And I should see one failed payment and new one ready to be paid

  Scenario: Retrying the payment with success
    Given I added product "PHP T-Shirt" to cart
    And I confirmed my order selecting PayPal Checkout Express payment method
    But I failed to pay
    And I am redirected back to the order payment page
    When I try to pay again
    And I sign in to PayPal and pay successfully
    Then I should be redirected back to the thank you page

  Scenario: Retrying the payment and failing
    Given I added product "PHP T-Shirt" to cart
    And I confirmed my order selecting PayPal Checkout Express payment method
    But I failed to pay
    And I am redirected back to the order payment page
    When I try to pay again
    And I sign in to PayPal but fail to pay
    Then I should be redirected back to the order payment page
    And I should see two failed payments and new one ready to be paid
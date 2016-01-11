@ui
Feature: Checkout with offline payment
  In order to pay with cash or by external means
  As a Customer
  I want to be able to complete checkout process without paying

  Background:
    Given that store is operating on the "United States" channel
    And default currency is USD
    And there is user "john@example.com" identified by "password123"
    And catalog has a product "PHP T-Shirt" priced at $19.99
    And store allows paying offline

  Scenario: Successfully placing an order
    Given I am logged in as "john@example.com"
    And I added product "PHP T-Shirt" to cart
    When I proceed selecting offline payment method
    And I confirm my order
    Then I should see see the thank you page
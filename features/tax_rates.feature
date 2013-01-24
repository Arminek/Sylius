Feature: Tax rates
    As a store owner
    I want to be able to configure tax rates
    In order to apply proper taxation to my merchandise

    Background:
        Given I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                 |
            | UK + Germany | country | United Kingdom, Germany |
            | USA          | country | USA                     |
          And there are following tax categories:
            | name        |
            | Clothing    |
            | Electronics |
            | Food        |
          And the following tax rates exist:
            | category    | zone         | name                  | amount |
            | Clothing    | UK + Germany | UK+DE Clothing Tax    | 20%    |
            | Electronics | UK + Germany | UK+DE Electronics Tax | 23%    |
            | Clothing    | USA          | US Clothing Tax       | 8%     |
            | Electronics | USA          | US Electronics Tax    | 10%    |

    Scenario: Seeing index of all tax rates
        Given I am on the dashboard page
         When I follow "Tax rates"
         Then I should be on the tax rate index page
          And I should see 4 tax rates in the list

    Scenario: Seeing empty index of tax rates
        Given there are no tax rates
          And I am on the tax rate index page
         Then I should see "There are no tax rates configured"

    Scenario: Accessing the tax rate creation form
        Given I am on the dashboard page
         When I follow "Tax rates"
          And I follow "Create tax rate"
         Then I should be on the tax rate creation page

    Scenario: Submitting invalid form without name
        Given I am on the tax rate creation page
         When I press "Create"
         Then I should still be on the tax rate creation page
          And I should see "Please enter tax rate name"

    Scenario: Trying to create tax leaving the amount field blank
        Given I am on the tax rate creation page
         When I fill in "Name" with "US Food Tax"
          And I leave "Amount" empty
          And I press "Create"
         Then I should still be on the tax rate creation page
          And I should see "Please enter tax rate amount."

    Scenario: Creating new tax rate
        Given I am on the tax rate creation page
          And I fill in "Name" with "US Food Tax"
          And I fill in "Amount" with "30"
          And I select "USA" from "Zone"
         When I press "Create"
         Then I should be on the page of tax rate "US Food Tax"
          And I should see "Rate has been successfully created."

    Scenario: Created tax rates appear in the list
        Given I created 18% tax "Food Tax" for category "Food" within zone "USA"
          And I go to the tax rate index page
         Then I should see 5 tax rates in the list
          And I should see tax rate with name "Food Tax" in that list

    Scenario: Accessing the tax rate editing form
        Given I am on the page of tax rate "US Clothing Tax"
          And I follow "Edit"
         Then I should be editing tax rate "US Clothing Tax"

    Scenario: Accessing the editing form from the list
        Given I am on the tax rate index page
          And I click "Edit" near "US Clothing Tax"
         Then I should be editing tax rate "US Clothing Tax"

    Scenario: Updating the tax rate
        Given I am on the page of tax rate "UK+DE Clothing Tax"
          And I follow "Edit"
         When I fill in "Name" with "General Tax"
          And I press "Save changes"
         Then I should be on the page of tax rate "General Tax"

    Scenario: Deleting tax rate
        Given I am on the page of tax rate "US Clothing Tax"
         When I follow "Delete"
         Then I should be on the tax rate index page
          And I should see "Rate has been successfully deleted."

    Scenario: Deleted tax rate disappears from the list
        Given I am on the page of tax rate "US Electronics Tax"
         When I follow "Delete"
         Then I should be on the tax rate index page
          And I should not see tax rate with name "US Electronics Tax" in that list

    Scenario: Displaying the tax rate amount on details page
        Given I am on the page of tax rate "US Clothing Tax"
         Then I should see "8%"

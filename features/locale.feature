Feature:
  In order to administrate locales
  As a administrator
  I need to be able to manage locales

  Background:
    Given There is a locale
    And There is an admin user "administrator"
    And I am logged in as "administrator"

  Scenario: List locales
    Given I am on "/locales/"
    Then I should see "English" in grid
    And I should not see "Lithuanian" in grid

  Scenario: Create locale
    Given I am on "/locales/"
    When I follow "Create"
    Then I should see an "form" element
    When I select "Lithuanian" from "Name"
    And I press "Create"
    Then I should see "Locale has been successfully created." flash message
    And I should see "Lithuanian" in grid

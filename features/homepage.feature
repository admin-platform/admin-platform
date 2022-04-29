Feature: Homepage
  In order to administrate application
  As a administrator
  I need to be able to see admin page

  Background:
    Given There is a locale
    And There is an admin user "admin"

  Scenario: Open admin page logged in
    Given I am logged in as "admin"
    When I go to "/"
    Then I should be on "/"
    And I should see "Admin platform"

  Scenario: Open admin page not logged in
    When I go to "/"
    Then I should be on "/login"
    And I should see a "form" element

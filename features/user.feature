Feature:
  In order to administrate application
  As a administrator
  I need to be able to manager users

  Background:
    Given There is a locale
    And There is an admin user "admin"
    And I am logged in as "admin"

  Scenario: List users
    Given I am on users page
    Then I should see "admin" in grid

  Scenario: Edit user
    Given There is an admin user "demo_user@example.com"
    And I am on users page
    And I have written down password hash of "demo_user@example.com"
    Then I should not see "edited_user@example.com" in grid
    When I edit "demo_user@example.com" from grid
    And I fill in the following:
      | Username   | edited_user@example.com |
      | First name | First name  |
      | Password   | new_password  |
    And I press "Save changes"
    Then I should see "Admin user has been successfully updated." flash message
    And I should see "edited_user@example.com" in grid
    And Password hash of "edited_user@example.com" should differ from hash i have written down

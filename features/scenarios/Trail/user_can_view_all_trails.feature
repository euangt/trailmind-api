Feature: User can view all trails
    In order to explore the details of all trails
    As a user
    I want to be able to view a list of all trails

    Background:
        Given there are the following trail:
            | Name         | Difficulty | Length |
            | Base Trail   | Easy       | 3.1    |
            | Second Trail | Moderate   | 5.4    |
            | Third Trail  | Hard       | 7.2    |

    @v1.0
    Scenario: User views a trail's information page
        When I request details of all trails in the system
        Then the platform should respond that the request was successful
        Then I should see following trails:
            | Name         | Difficulty | Length |
            | Base Trail   | Easy       | 3.1    |
            | Second Trail | Moderate   | 5.4    |
            | Third Trail  | Hard       | 7.2    |
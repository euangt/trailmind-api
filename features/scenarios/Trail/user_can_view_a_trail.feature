Feature: User can view a trail

    In order to explore the details of a trail
    As a user
    I want to be able to view a trail's information page

    Background:
        Given there is the following trail:
            | Name       | Difficulty | Length |
            | Base Trail | Easy       | 3      |

    @v1.0
    Scenario: User views a trail's information page
        When I request details of the trail "Base Trail"
        Then the platform should respond that the request was successful
        Then I should see the trail information:
            | Name       | Difficulty | Length |
            | Base Trail | Easy       | 3      |
Feature: User can view their hikes

    In order to review my hiking history
    As a user
    I want to be able to view all hikes I have recorded

    @v1.0 @user
    Scenario: User can view their recorded hikes
        Given there is the following trail:
            | Name          | Difficulty | Length |
            | Mountain Path | Hard       | 12.5   |
        And I record a hike on the "Mountain Path" trail with the following details:
            | Start Date          | End Date            |
            | 2024-06-01 08:00:00 | 2024-06-01 16:00:00 |
        When I request my hikes
        Then the platform should respond that the request was successful
        And I should see the following hikes in the response:
            | Start Date          | End Date            |
            | 2024-06-01 08:00:00 | 2024-06-01 16:00:00 |

    @v1.0 @user
    Scenario: User with no hikes sees an empty list
        When I request my hikes
        Then the platform should respond that the request was successful
        And I should see no hikes in the response

    @v1.0
    Scenario: Unauthenticated user cannot view hikes
        When I request my hikes
        Then the platform should respond that the attempt failed and the user is unauthorised

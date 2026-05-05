Feature: User can record a hike

    In order to track my hiking activity
    As a user
    I want to be able to record a hike against a trail

    Background:
        Given there is the following trail:
            | Name          | Difficulty | Length |
            | Mountain Path | Hard       | 12.5   |

    @v1.0 @user
    Scenario: User records a hike
        When I record a hike on the "Mountain Path" trail with the following details:
            | Start Date          | End Date            |
            | 2024-06-01 08:00:00 | 2024-06-01 16:00:00 |
        Then the platform should respond that the hike was recorded
        And I should see the hike in the response

    @v1.0 @user
    Scenario: User cannot record a hike where the end date is before the start date
        When I record a hike on the "Mountain Path" trail with the following details:
            | Start Date          | End Date            |
            | 2024-06-01 16:00:00 | 2024-06-01 08:00:00 |
        Then the platform should respond that the request had unprocessable content

    @v1.0
    Scenario: Unauthenticated user cannot record a hike
        When I record a hike on the "Mountain Path" trail with the following details:
            | Start Date          | End Date            |
            | 2024-06-01 08:00:00 | 2024-06-01 16:00:00 |
        Then the platform should respond that the attempt failed and the user is unauthorised

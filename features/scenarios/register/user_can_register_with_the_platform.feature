Feature: User Registration
    In order to access the platforms features
    As a new user
    I want to register with the platform

    @v1.0
    Scenario: User can register with the platform
        When I request to register with the following details:
            | Email                | Name     | Password    | Username |
            | john.doe@example.com | John Doe | password123 | john_doe |
        Then the platform should respond that the request was successful without additional data
        And there should be a user in the system with the following details:
            | Email                | Name     | Username |
            | john.doe@example.com | John Doe | john_doe |
        And the user 'john.doe@example.com' should have a password set

    @v1.0
    Scenario: User cannot register without an email
        When I request to register with the following details:
            | Name     | Password    | Username |
            | John Doe | password123 | john_doe |
        Then the platform should respond that the request had unprocessable content

    @v1.0
    Scenario: User cannot register without a name
        When I request to register with the following details:
            | Email                | Password    | Username |
            | john.doe@example.com | password123 | john_doe |
        Then the platform should respond that the request had unprocessable content

    @v1.0
    Scenario: User cannot register without a password
        When I request to register with the following details:
            | Email                | Name     | Username |
            | john.doe@example.com | John Doe | john_doe |
        Then the platform should respond that the request had unprocessable content

    @v1.0
    Scenario: User cannot register without a username
        When I request to register with the following details:
            | Email                | Name     | Password    |
            | john.doe@example.com | John Doe | password123 |
        Then the platform should respond that the request had unprocessable content
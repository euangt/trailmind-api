Feature: user can authenticate
    In order to be able to access more fine grained information on the platform
    As a user
    I would like to authenticate with the system

    Background:
        Given there is a user "user@user.com" with password "password"

    @v1.0
    Scenario: User can authenticate
        When the user "user@user.com" authenticates with the password "password"
        Then the platform should respond that the request was successful
        And the platform should respond with a valid access token

    @v1.0 @insulated
    Scenario: User cannot authenticate if password is not supplied
        When the user "user@user.com" authenticates with no password
        Then the platform should respond that the request had unprocessable content

    @v1.0
    Scenario: User cannot authenticate with the wrong password
        When the user "user@user.com" authenticates with the password "wrong password"
        Then the platform should respond that the attempt failed and the user is unauthorised

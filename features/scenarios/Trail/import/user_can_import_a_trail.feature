Feature: User can import a trail

    In order to build out the platforms trail database
    As a user
    I want to be able to import a trail

    Background:
        Given there is the following trail:
            | Name             | Difficulty | Length |
            | Offas Dyke Trail | Hard       | 3.1    |

    @v1.0
    Scenario: User can import a trail
        Given there is a GPX file for 'offas_dyke.gpx'
        When I request to import the 'offas_dyke.gpx' GPX file for "Offas Dyke Trail"
        Then the platform should respond that the trail was created
        And the "Offas Dyke Trail" should have trail points attached to it
        And the "Offas Dyke Trail" should have the start point
            | Latitude    | Longitude    | Elevation |
            | 51.63253954 | -2.648426341 | 20.43     |
        And the "Offas Dyke Trail" should have the end point
            | Latitude     | Longitude    | Elevation |
            | 51.742635174 | -2.668300923 | 5.63      |
        And the "Offas Dyke Trail" should have a route set

    @v1.0
    Scenario: User cannot import a trail without a file
        When I request to import a GPX file for "Offas Dyke Trail"
        Then the platform should respond that the request was bad
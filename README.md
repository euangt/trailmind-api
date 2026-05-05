# Trailmind

Trailmind is a Symfony application for exploring hiking trails and supporting the data and account flows around them.

At a high level, the app combines two things:

- a public-facing homepage that introduces the Trailmind brand and browsing experience
- a JSON API for trail data, user registration, authentication, and trail point imports

## What The App Does

Trailmind is aimed at a trail-discovery experience. It gives users a way to browse available trails, inspect an individual trail, create an account, sign in, and support trail data ingestion behind the scenes.

The current API surface includes:

- viewing the full trail collection
- viewing a single trail by id
- registering a user account
- authenticating a user and issuing an access token
- reauthenticating a user with a refresh token
- importing trail points into an existing trail

## Domain Focus

The codebase is centered on a few core concerns:

- trails and trail detail
- user accounts and authentication
- trail geometry or point import workflows
- API responses and DTO-driven request handling

## In Plain Terms

If you ignore the implementation details, this app is the early foundation of a hiking product: a place where users can discover trails, identify themselves, and where the system can gradually grow richer trail data and interactive features over time.

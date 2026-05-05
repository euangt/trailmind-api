<?php

namespace TestingInfrastructure\Context\Authentication;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use TestingInfrastructure\Services\ServiceProvider;
use TestingInfrastructure\Context\Request\RequestContext;
use TestingInfrastructure\Context\Response\ResponseContext;
use Trailmind\User\User;

class AuthenticateContext implements Context
{
    const CLIENT_ID = '9cbf6836-f2a6-4cf7-893c-8881c540714f';
    const CLIENT_SECRET = 'OxdUb&0bmR9c';
    const I_USER_EMAIL = "me@trailmind.club";
    const I_USER_PASSWORD = "password";
    const I_USER_ID = "9bac9a9f-5aaf-49af-bdb4-b7de9a62395c";
    const I_USER_NAME = "Me Hiker";

    private ServiceProvider $services;
    private RequestContext $requestContext;
    private ResponseContext $responseContext;

    public function __construct(
        KernelInterface $kernel,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        $this->services = new ServiceProvider($kernel);
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function getOtherContexts(BeforeScenarioScope $scope)
    {
        $this->requestContext = $scope->getEnvironment()->getContext('TestingInfrastructure\Context\Request\RequestContext');
        $this->responseContext = $scope->getEnvironment()->getContext('TestingInfrastructure\Context\Response\ResponseContext');
    }

    /**
     * @BeforeScenario
     */
    public function ensureUserIsAuthenticated(BeforeScenarioScope $scope)
    {
        foreach ($scope->getScenario()->getTags() as $tag) {
            switch($tag) {
                case 'user':
                    $this->ensureUserIsAuthenticated();
                    break;
                default:
                    //Do nothing
            }
        }
    }

    public function setUpIUser(): User
    {
        $user = new User(
            self::I_USER_EMAIL,
            self::I_USER_NAME,
            'username'
        );

        $this->services->getUserRepository()->save($user);

        return $user;
    }

    #[Given('there is a user :email')]
    public function thereIsAUser(string $email): void
    {
        $user = new User(
            $email,
            'name',
            'username'
        );
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

        $this->services->getUserRepository()->save($user);
    }

    #[Given('there is a user :email with password :password')]
    public function thereIsAUserWithPassword(string $email, string $password): void
    {
        $user = new User(
            $email,
            'name',
            'username'
        );
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->services->getUserRepository()->save($user);
    }

    #[When('the user :email authenticates with the password :password')]
    public function theUserAuthenticatesWithThePassword(string $email, string $password): void
    {
        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            '/authenticate',
            [
                'password' => $password,
                'email' => $email
            ]
        );
    }

    #[When('the user :email authenticates with no password')]
    public function theUserAuthenticatesWithNoPassword(string $email): void
    {
        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            '/authenticate',
            [
                'email' => $email
            ]
        );
    }

    #[Then('the platform should respond with a valid access token')]
    public function thePlatformShouldRespondWithAValidAccessToken(): void
    {
        $responseData = $this->responseContext->getResponseAsObject();

        assert(isset($responseData->access_token), 'Response does not contain access_token');
        assert(isset($responseData->refresh_token), 'Response does not contain refresh_token');
        assert(isset($responseData->expires_in), 'Response does not contain expires_in');
    }
}

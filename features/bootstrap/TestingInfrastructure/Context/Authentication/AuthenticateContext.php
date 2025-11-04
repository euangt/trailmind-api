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
use TestingInfrastructure\Services\ServiceProvider;
use TestingInfrastructure\Context\Request\RequestContext;
use TestingInfrastructure\Context\Response\ResponseContext;
use Trailmind\User\User;

class AuthenticateContext implements Context
{
    private ServiceProvider $services;
    private RequestContext $requestContext;
    private ResponseContext $responseContext;

    public function __construct(
        KernelInterface $kernel
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

    #[Given('there is a user :email with password :password')]
    public function thereIsAUserWithPassword($email, $password): void
    {
        $user = new User(
            $email,
            'name',
            'username'
        );
        $user->setPassword($password);

        $this->services->getUserRepository()->save($user);
    }

    #[When('the user :user authenticates with the password :password')]
    public function theUserAuthenticatesWithThePassword($user, $password): void
    {
        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            '/authenticate',
            [ 'password' => $password ]
        );
    }

    #[When('the user :arg1 authenticates with no password')]
    public function theUserAuthenticatesWithNoPassword($arg1): void
    {
        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            '/authenticate',
            []
        );
    }

    #[Then('the platform should respond with a valid access token')]
    public function thePlatformShouldRespondWithAValidAccessToken(): void
    {
        $responseData = $this->responseContext->getResponseData();

        assert(isset($responseData->access_token), 'Response does not contain access_token');
        assert(isset($responseData->refresh_token), 'Response does not contain refresh_token');
        assert(isset($responseData->expires_in), 'Response does not contain expires_in');
    }


    #[Then('the platform should respond that the attempt failed and the user is unauthorised')]
    public function thePlatformShouldRespondThatTheAttemptFailedAndTheUserIsUnauthorised(): void
    {
        throw new PendingException();
    }
}

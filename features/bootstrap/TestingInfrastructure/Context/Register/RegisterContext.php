<?php

namespace TestingInfrastructure\Context\Register;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use Symfony\Component\HttpKernel\KernelInterface;
use Trailmind\Trail\Trail;
use TestingInfrastructure\Services\ServiceProvider;
use TestingInfrastructure\Context\Request\RequestContext;
use TestingInfrastructure\Context\Response\ResponseContext;

class RegisterContext implements Context
{
    private ServiceProvider $services;
    private RequestContext $requestContext;

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
    }

    #[When('I request to register with the following details:')]
    public function iRequestToRegisterWithTheFollowingDetails(TableNode $table): void
    {
        $userData = $table->getColumnsHash()[0];
        $requestBody = $this->buildRequestBody($userData);

        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            '/register',
            $requestBody
        );
    }

    private function buildRequestBody(array $userData): array
    {
        $data = [
            'email' => $userData['Email'] ?: $userData['Email'],
            'name' => $userData['Name'] ?: $userData['Name'],
            'password' => $userData['Password'] ?: $userData['Password'],
            'username' => $userData['Username'] ?: $userData['Username'],
        ];

        return $data;
    }
}

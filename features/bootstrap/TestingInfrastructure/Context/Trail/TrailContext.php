<?php

namespace TestingInfrastructure\Context\Trail;

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

/**
 * Defines application features from the specific context.
 */
class TrailContext implements Context
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

    #[Given('there is the following trail:')]
    public function thereIsTheFollowingTrail(TableNode $table): void
    {
        $trailData = $table->getColumnsHash()[0];

        $trail = new Trail(
            $trailData['Name'],
            $trailData['Difficulty'],
            $trailData['Length']
        );

        $this->services->getTrailRepository()->save($trail);
    }

    #[When('I request details of the trail :trailName')]
    public function iRequestDetailsOfTheTrail($trailName): void
    {
        $trail = $this->services->getTrailRepository()->findOneByName($trailName);

        $this->requestContext->makeVersionedJsonRequest(
            'GET',
            '/trail/' . $trail->getId()
        );
    }

    #[Then('I should see the trail information:')]
    public function iShouldSeeTheTrailInformation(TableNode $table): void
    {
        $expectedTrail = $table->getColumnsHash()[0];
        $responseTrail = $this->responseContext->getResponseAsObject();

        assert($responseTrail->name === $expectedTrail['Name']);
        assert($responseTrail->difficulty === $expectedTrail['Difficulty']);
        assert($responseTrail->length === (float)$expectedTrail['Length']);
    }
}

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
class ImportTrailContext implements Context
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

    /**
     * @Transform :trail
     */
    public function castUserEmailToUser($trail)
    {
        try {
            return $this->services->getTrailRepository()->findOneByName($trail);
        } catch (TrailNotFoundException $unfe) {
            throw new \UnexpectedValueException("No trail found with name {$trail}");
        }
    }

    #[Given('there is a GPX file for :filepath')]
    public function thereIsAGpxFileFor($filepath): void
    {
        $path = getcwd() . "/fixtures/trails/" . $filepath;
        assert(file_exists($path));
    }

    #[When('I request to import the :filename GPX file for :trail')]
    public function iRequestToImportTheGpxFileFor($filename, $trail): void
    {
        $body = ['filename'=>$filename];

        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            "/trail/{$trail->getId()}/import-trail-points",
            $body
        );
    }

    #[When('I request to import a GPX file for :trail')]
    public function iRequestToImportAGpxFileFor($trail): void
    {
        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            "/trail/{$trail->getId()}/import-trail-points"
        );
    }
}
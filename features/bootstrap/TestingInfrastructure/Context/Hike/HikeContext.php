<?php

namespace TestingInfrastructure\Context\Hike;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Then;
use Behat\Step\When;
use Symfony\Component\HttpKernel\KernelInterface;
use TestingInfrastructure\Context\Request\RequestContext;
use TestingInfrastructure\Context\Response\ResponseContext;
use TestingInfrastructure\Services\ServiceProvider;

class HikeContext implements Context
{
    private ServiceProvider $services;
    private RequestContext $requestContext;
    private ResponseContext $responseContext;

    public function __construct(KernelInterface $kernel)
    {
        $this->services = new ServiceProvider($kernel);
    }

    /**
     * @BeforeScenario
     */
    public function getOtherContexts(BeforeScenarioScope $scope): void
    {
        $this->requestContext = $scope->getEnvironment()->getContext('TestingInfrastructure\Context\Request\RequestContext');
        $this->responseContext = $scope->getEnvironment()->getContext('TestingInfrastructure\Context\Response\ResponseContext');
    }

    #[When('I record a hike on the :trailName trail with the following details:')]
    public function iRecordAHikeOnTheTrailWithTheFollowingDetails(string $trailName, TableNode $table): void
    {
        $data = $table->getColumnsHash()[0];
        $trail = $this->services->getTrailRepository()->findOneByName($trailName);

        $this->requestContext->makeVersionedJsonRequest(
            'POST',
            '/hike',
            [
                'trailId'   => $trail->getId(),
                'startDate' => $data['Start Date'],
                'endDate'   => $data['End Date'],
            ]
        );
    }

    #[When('I request my hikes')]
    public function iRequestMyHikes(): void
    {
        $this->requestContext->makeVersionedJsonRequest('GET', '/hikes');
    }

    #[Then('the platform should respond that the hike was recorded')]
    public function thePlatformShouldRespondThatTheHikeWasRecorded(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        if ($receivedStatusCode !== 201) {
            throw new \UnexpectedValueException("Unexpected Status Code: {$receivedStatusCode}, expected: 201");
        }
    }

    #[Then('I should see the hike in the response')]
    public function iShouldSeeTheHikeInTheResponse(): void
    {
        $response = $this->responseContext->getResponseAsObject();

        assert(isset($response->id), 'Response does not contain a hike id');
        assert(isset($response->trail), 'Response does not contain a trail');
        assert(isset($response->startDate), 'Response does not contain startDate');
        assert(isset($response->endDate), 'Response does not contain endDate');
    }

    #[Then('I should see the following hikes in the response:')]
    public function iShouldSeeTheFollowingHikesInTheResponse(TableNode $table): void
    {
        $expectedHikes = $table->getColumnsHash();
        $responseHikes = $this->responseContext->getResponseAsObject()->hikes;

        assert(
            count($responseHikes) === count($expectedHikes),
            sprintf('Expected %d hike(s) but found %d', count($expectedHikes), count($responseHikes))
        );

        foreach ($expectedHikes as $index => $expected) {
            assert(
                $responseHikes[$index]->startDate === $expected['Start Date'],
                "startDate mismatch at index {$index}"
            );
            assert(
                $responseHikes[$index]->endDate === $expected['End Date'],
                "endDate mismatch at index {$index}"
            );
        }
    }

    #[Then('I should see no hikes in the response')]
    public function iShouldSeeNoHikesInTheResponse(): void
    {
        $responseHikes = $this->responseContext->getResponseAsObject()->hikes;
        assert(count($responseHikes) === 0, 'Expected no hikes but found some');
    }

    private function getSession()
    {
        return $this->requestContext->getSession();
    }
}

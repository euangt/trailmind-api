<?php

namespace TestingInfrastructure\Context\Request;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use RuntimeException;
use TestingInfrastructure\Context\Authentication\AuthenticateContext;

class RequestContext extends RawMinkContext
{
    const REQUEST_DOMAIN = 'api.develop.trailmind.co.uk';

    private $featureContext;

    private ?string $authToken = null;

    /**
     * @BeforeScenario
     */
    public function getOtherContexts(BeforeScenarioScope $scope)
    {
        $this->featureContext = $scope->getEnvironment()->getContext('TestingInfrastructure\Context\Feature\FeatureContext');
    }

    /**
     * @BeforeScenario
     */
    public function resetAuthToken(): void
    {
        $this->authToken = null;
    }

    public function setAuthToken(string $token): void
    {
        $this->authToken = $token;
    }

    /**
     * Make a versioned JSON request using the current scenario version tag.
     */
    public function makeVersionedJsonRequest($method, $path, $body = [], $withClientHeaders = true)
    {
        if (empty($this->featureContext->version)) {
            throw new RuntimeException('Can\'t make a versioned request without version. Please add @vX.Y to the scenario.');
        }

        $url = sprintf('http://%s/%s/%s', self::REQUEST_DOMAIN, $this->featureContext->version, ltrim($path, '/'));
        $this->makeHttpJsonRequest($method, $url, $this->authToken, $body);
    }

    /**
     * Make a versioned JSON request using an explicit version string, bypassing the scenario tag.
     * Used internally for pre-scenario setup such as authentication.
     */
    public function makeVersionedJsonRequestWithVersion(string $method, string $path, string $version, array $body = []): void
    {
        $url = sprintf('http://%s/%s/%s', self::REQUEST_DOMAIN, $version, ltrim($path, '/'));
        $this->makeHttpJsonRequest($method, $url, null, $body);
    }

    private function makeHttpJsonRequest(
        $method,
        $url,
        $token = null,
        $body = null,
    ) {
        $client = $this->getSession()->getDriver()->getClient();
        $client->restart();
        $server = [];

        if (isset($token)) {
            $server['HTTP_AUTHORIZATION'] = "Bearer $token";
        }

        $server['HTTP_CLIENT_ID'] = AuthenticateContext::CLIENT_ID;
        $server['HTTP_CLIENT_SECRET'] = AuthenticateContext::CLIENT_SECRET;

        $client->jsonRequest(
            $method,
            $url,
            $body,
            $server
        );
    }
}

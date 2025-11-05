<?php

namespace TestingInfrastructure\Context\Request;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use RuntimeException;
use TestingInfrastructure\Context\Authentication\AuthenticateContext;

class RequestContext extends RawMinkContext
{
    const REQUEST_DOMAIN = 'api.develop.trailmind.co.uk';

    /**
     * @var FeatureContext
     */
    private $featureContext;

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function getOtherContexts(BeforeScenarioScope $scope)
    {
        $this->featureContext = $scope->getEnvironment()->getContext('TestingInfrastructure\Context\Feature\FeatureContext');
    }

    /**
     * @param string      $method            The HTTP verb we are using to make this request
     * @param string      $url               The endpoint we are looking to hit
     * @param string|null $body              Any request body that we are sending with the request
     */
    public function makeVersionedJsonRequest($method, $path, $body = [], $withClientHeaders = true)
    {
        if (empty($this->featureContext->version)) {
            throw new RuntimeException('Can\'t make a versioned request without version. Please add @vX.Y to the scenario.');
        }

        $url = sprintf('http://%s/%s/%s', self::REQUEST_DOMAIN, $this->featureContext->version, ltrim($path, '/'));
        $this->makeHttpJsonRequest($method, $url, null, $body);
    }

    /**
     * @param string      $method            The HTTP verb we are using to make this request
     * @param string      $url               The endpoint we are looking to hit
     * @param string|null $token             The token to authenticate the request
     * @param string|null $body              Any request body that we are sending with the request
     */
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
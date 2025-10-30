<?php

namespace TestingInfrastructure\Context\Response;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Then;

class ResponseContext extends RawMinkContext
{
    /**
     * @return \mixed
     */
    public function getResponseAsObject()
    {
        return $this->deserializeJSON($this->getSession()->getPage()->getContent());
    }

    /**
     * @return string
     */
    public function getResponseAsJson()
    {
        return $this->getSession()->getPage()->getContent();
    }

    /**
     * @param string $jsonString
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function deserializeJSON($jsonString)
    {
        $data = json_decode($jsonString);
        if (!isset($data)) {
            throw new \Exception("The string `{$jsonString}` is not a valid JSON representation");
        }
        return $data;
    }

    #[Then('the platform should respond that the request was successful')]
    public function thePlatformShouldRespondThatTheRequestWasSuccessful(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        if ($receivedStatusCode === 500) {
            $handle = fopen('stacktrace.html', 'w');
            fwrite($handle, $this->getResponseAsJson());
            fclose($handle);
        }
        if ($receivedStatusCode !== 200) {
            $response = $this->getResponseAsObject();
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ": " . $response->message);
        }
    }
}
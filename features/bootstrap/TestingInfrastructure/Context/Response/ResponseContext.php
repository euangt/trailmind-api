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
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 200) {
            $response = $this->getResponseAsObject();
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ": " . $response->message);
        }
    }

    #[Then('the platform should respond that the request was successful without additional data')]
    public function thePlatformShouldRespondThatTheRequestWasSuccessfulWithoutAdditionalData(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 204) {
            $response = $this->getResponseAsObject();
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ": " . $response->message);
        }
    }

    #[Then('the platform should respond that the request was bad')]
    public function thePlatformShouldRespondThatTheRequestWasBad(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 400) {
            $response = $this->getResponseAsObject();
            var_dump($this->getResponseAsJson());
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ": " . $response->message);
        }
    }

    #[Then('the platform should respond that the request had unprocessable content')]
    public function thePlatformShouldRespondThatTheRequestHadUnprocessableContent(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 422) {
            $response = $this->getResponseAsObject();
            var_dump($this->getResponseAsJson());
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ": " . $response->message);
        }
    }

    private function generateStacktraceFile(int $receivedStatusCode): void
    {
        if ($receivedStatusCode === 500) {
            $handle = fopen('stacktrace.html', 'w');
            fwrite($handle, $this->getResponseAsJson());
            fclose($handle);
        }
    }
}
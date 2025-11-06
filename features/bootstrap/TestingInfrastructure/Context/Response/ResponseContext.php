<?php

namespace TestingInfrastructure\Context\Response;

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
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ' expected: 200');
        }
    }

    #[Then('the platform should respond that the trail was created')]
    public function thePlatformShouldRespondThatTheTrailWasCreated(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 201) {
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ' expected: 204');
        }
    }

    #[Then('the platform should respond that the request was successful without additional data')]
    public function thePlatformShouldRespondThatTheRequestWasSuccessfulWithoutAdditionalData(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 204) {
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ' expected: 204');
        }
    }

    #[Then('the platform should respond that the request was bad')]
    public function thePlatformShouldRespondThatTheRequestWasBad(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 400) {
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ' expected: 400');
        }
    }

    #[Then('the platform should respond that the attempt failed and the user is unauthorised')]
    public function thePlatformShouldRespondThatTheAttemptFailedAndTheUserIsUnauthorised(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 401) {
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ' expected: 401');
        }
    }

    #[Then('the platform should respond that the request had unprocessable content')]
    public function thePlatformShouldRespondThatTheRequestHadUnprocessableContent(): void
    {
        $receivedStatusCode = $this->getSession()->getStatusCode();
        $this->generateStacktraceFile($receivedStatusCode);
        if ($receivedStatusCode !== 422) {
            throw new \UnexpectedValueException("Unexpected Status Code: " . $receivedStatusCode . ' expected: 422');
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
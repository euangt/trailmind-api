<?php

namespace TestingInfrastructure\Context\Feature;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use TestingInfrastructure\Services\ServiceProvider;
use TestingInfrastructure\Context\AuthenticatedContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * @var ServiceProvider
     */
    private static $services;

    /**
     * @var string
     */
    public $version;

    /**
     * @var EntityManagerInterface
     */
    private static $manager;

    /**
     *
     */
    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $manager,
    ) {
        self::$services = new ServiceProvider($kernel);
        self::$manager = $manager;
    }

    /**
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope
     */
    public function captureScenarioInformation(BeforeScenarioScope $scope)
    {
        foreach ($scope->getScenario()->getTags() as $tag) {
            if (preg_match('/^v\d+\.\d+$/', $tag) === 1) {
                $this->version = $tag;
            }
        }
    }
}
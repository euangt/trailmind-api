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
    // these tables are not edited during the test suite, so only need
    // to be populated once
    private const CORE_TABLES = [
        'clients',
        'roles',
        'doctrine_migration_versions',
    ];

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

    /**
     * @BeforeScenario
     */
    public function resetDatabase()
    {
        $connection = self::$manager->getConnection();
        $this->dropData($connection);
    }

    /**
     * Remove any data that was created in the previous scenario so that we are
     * working with a clear data set
     */
    private function dropData($connection)
    {
        $tables = array_filter($connection->createSchemaManager()->listTableNames(), function($table) {
            return !in_array($table, self::CORE_TABLES);
        });

        //  required as product_categories has a self referencing foreign key
        // $stmt = $connection->prepare("SET FOREIGN_KEY_CHECKS=0;");
        // $stmt->executeQuery();

        foreach ($tables as $table) {
            $stmt = $connection->prepare("DELETE FROM " . $table);
            $stmt->executeQuery();
        }

        // // make sure we put this back
        // $stmt = $connection->prepare("SET FOREIGN_KEY_CHECKS=1;");
        // $stmt->executeQuery();
    }
}
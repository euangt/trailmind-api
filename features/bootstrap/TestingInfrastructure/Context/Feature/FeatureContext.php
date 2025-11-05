<?php

namespace TestingInfrastructure\Context\Feature;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TestingInfrastructure\Services\ServiceProvider;
use TestingInfrastructure\Context\Authentication\AuthenticateContext;
use Trailmind\Access\Client;

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
        'oauth2_access_tokens',
        'doctrine_migration_versions',
    ];

    private const UP_TO_DATE = "Up-to-date";

    /**
     * @var Application
     */
    private static $application;

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

    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $manager,
    ) {
        self::$services = new ServiceProvider($kernel);
        self::$application = new Application($kernel);
        self::$manager = $manager;
    }

    /**
     * @BeforeSuite
     *
     * @param BeforeSuiteScope $scope
     */
    public static function resetDBSchema(BeforeSuiteScope $scope)
    {
        // resetting the DB schema takes a long time, so to avoid this
        // we check whether we actually need to reset it before doing so
        if (!self::databaseIsCurrent()) {
            self::runMigrations();

            self::populateClient();
        }
    }

    /**
     * returns true if all migrations in the ./src/Migrations directory have been run
     * against the current database
     */
    private static function databaseIsCurrent(): bool {
        try {
            $result = self::runCommand('doctrine:migrations:up-to-date');
        } catch (ConnectionException $ce) {
            // this happens if the database doesn't exist
            return false;
        }
        return str_contains($result, self::UP_TO_DATE);
    }

    private static function runMigrations() {
        // drop the db before we start so we can run all the migrations
        self::runCommand('doctrine:database:drop', ['--force' => true]);
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:migrations:migrate');
    }

    private static function populateClient()
    {
        // We need to define the client so that authentication will work
        $client = new Client(AuthenticateContext::CLIENT_ID, 'Trailmind Insights');
        $client->setSecret(AuthenticateContext::CLIENT_SECRET);
        $client->setRedirect("/");
        self::save($client);
    }

    private static function runCommand($command, $args = []): string {
        $command = self::$application->find($command);
        $input = new ArrayInput($args);
        $input->setInteractive(false);
        $output = new BufferedOutput();
        $command->run($input, $output);
        return $output->fetch();
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

        // Delete token tables in correct order due to foreign key constraints
        $tokenTables = [
            'oauth2_refresh_token',
            'oauth2_access_token', 
            'oauth2_authorization_code',
            'refresh_tokens',
            'access_tokens'
        ];
        
        foreach ($tokenTables as $table) {
            if (in_array($table, $tables)) {
                $stmt = $connection->prepare("DELETE FROM " . $table);
                $stmt->executeQuery();
            }
        }

        // Then delete remaining tables
        foreach ($tables as $table) {
            if (!in_array($table, $tokenTables)) {
                $stmt = $connection->prepare("DELETE FROM " . $table);
                $stmt->executeQuery();
            }
        }
    }

    private static function save($entity) {
        self::$manager->persist($entity);
        self::$manager->flush();
    }
}
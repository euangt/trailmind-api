<?php

namespace TestingInfrastructure\Context\User;

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
use TestingInfrastructure\Context\Response\ResponseContext;
use Trailmind\User\Exception\UserNotFoundException;

class UserContext implements Context
{
    private ServiceProvider $services;

    public function __construct(
        KernelInterface $kernel
    ) {
        $this->services = new ServiceProvider($kernel);
    }

    /**
     * @Transform :user
     */
    public function castUserEmailToUser($user)
    {
        try {
            return $this->services->getUserRepository()->findOneByEmail($user);
        } catch (UserNotFoundException $unfe) {
            try {
                return $this->services->getUserRepository()->findOneByName($user);
            } catch (UserNotFoundException $unfe) {
                throw new \UnexpectedValueException("No user found with email or name {$user}");
            }
        }
    }

    #[Then('there should be a user in the system with the following details:')]
    public function thereShouldBeAUserInTheSystemWithTheFollowingDetails(TableNode $table): void
    {
        $expectedUser = $table->getColumnsHash()[0];
        try {
            $user = $this->services->getUserRepository()->findOneByEmail($expectedUser['Email']);
        } catch (UserNotFoundException $e) {
            throw new \UnexpectedValueException("No user found with email {$expectedUser['Email']}");
        }

        assert($user->getEmail() === $expectedUser['Email'], "Expected user email to be {$expectedUser['Email']} but found {$user->getEmail()}");
        assert($user->getName() === $expectedUser['Name'], "Expected user name to be {$expectedUser['Name']} but found {$user->getName()}");
        assert($user->getUsername() === $expectedUser['Username'], "Expected user username to be {$expectedUser['Username']} but found {$user->getUsername()}");
    }

    #[Then('the user :user should have a password set')]
    public function theUserShouldHaveAPasswordSet($user): void
    {
        assert($user->getPassword(), "Expected user {$user->getName()} to have a password set but none was found");
    }
}

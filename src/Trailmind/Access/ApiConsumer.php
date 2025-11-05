<?php

namespace Trailmind\Access;

use Symfony\Component\Security\Core\User\UserInterface;

interface ApiConsumer extends UserInterface
{
    /**
     * @param string $roleToCheck
     *
     * @return bool
     */
    public function hasRole(string $roleToCheck): bool;

    /**
     * @param string $role
     */
    public function addRole(string $role): void;

    /**
    * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return bool
     */
    public function isBuyersGroupAdmin(): bool;

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void;
}

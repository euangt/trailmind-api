<?php

namespace Trailmind\Access;

use Symfony\Component\Security\Core\User\UserInterface;

interface ApiConsumer extends UserInterface
{
    public function hasRole(string $roleToCheck): bool;

    public function addRole(string $role): void;

    public function isAdmin(): bool;

    public function isBuyersGroupAdmin(): bool;

    public function setRoles(array $roles): void;
}

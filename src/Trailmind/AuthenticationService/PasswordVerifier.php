<?php

namespace Trailmind\AuthenticationService;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Trailmind\User\User;

class PasswordVerifier
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function verifyPassword(User $user, string $plainTextPassword): bool
    {
        // A user will have to work very hard to get here without a password but it has been done.
        if (is_null($user->getPassword())) {
            return false;
        }

        return $this->passwordHasher->isPasswordValid($user, $plainTextPassword);
    }
}
<?php

namespace Trailmind\User;

interface UserRepository
{
    public function save(User $user): void;

    public function findOneById(string $id): User;

    public function findOneByEmail(string $email): User;
}
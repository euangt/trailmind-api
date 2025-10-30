<?php

namespace Trailmind\User;

interface UserRepository
{
    /**
     * @param User $user
     * 
     * @return void
     */
    public function save(User $user): void;

    /**
     * @param string $id
     * 
     * @return User
     */
    public function findOneById(string $id): User;
}
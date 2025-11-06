<?php

namespace Trailmind\Access;

use Trailmind\Access\Exception\AccessTokenNotFoundException;
use Trailmind\User\User;

interface AccessTokenRepository
{
    /**
     * @throws AccessTokenNotFoundException
     */
    public function findOneById(string $id): ?AccessToken;

    /**
     * @throws AccessTokenNotFoundException
     */
    public function findUnrevokedByUser(User $user): ?AccessToken;

    public function save(AccessToken $accessToken): void;
}

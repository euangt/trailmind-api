<?php

namespace Trailmind\Access;

use Trailmind\Access\Exception\AccessTokenNotFoundException;
use Trailmind\User\User;

interface AccessTokenRepository
{
    /**
     * @param string $id
     *
     * @return AccessToken
     *
     * @throws AccessTokenNotFoundException
     */
    public function findOneById(string $id): ?AccessToken;
    
    /**
     * @param User $user
     *
     * @return AccessToken
     *
     * @throws AccessTokenNotFoundException
     */
    public function findUnrevokedByUser(User $user): ?AccessToken;

    
    public function save(AccessToken $accessToken): void;
}

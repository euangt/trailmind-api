<?php

namespace Trailmind\Access;

use Trailmind\Access\Exception\RefreshTokenNotFoundException;

interface RefreshTokenRepository
{
    /**
     * @throws RefreshTokenNotFoundException
     */
    public function findOneById(string $id): ?RefreshToken;

    /**
     * @throws RefreshTokenNotFoundException
     */
    public function findOneByAccessToken(AccessToken $accessToken): ?RefreshToken;

    public function save(RefreshToken $refreshToken): void;
}

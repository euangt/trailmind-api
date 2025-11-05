<?php

namespace Trailmind\Access;

use Trailmind\Access\Exception\RefreshTokenNotFoundException;

interface RefreshTokenRepository
{
    /**
     * @param string $id
     *
     * @return RefreshToken
     *
     * @throws RefreshTokenNotFoundException
     */
    public function findOneById(string $id): ?RefreshToken;

    /**
     * @param AccessToken $accessToken
     *
     * @return RefreshToken
     *
     * @throws RefreshTokenNotFoundException
     */
    public function findOneByAccessToken(AccessToken $accessToken): ?RefreshToken;

    /**
     * @param RefreshToken $refreshToken
     *
     * @return void
     */
    public function save(RefreshToken $refreshToken): void;
}

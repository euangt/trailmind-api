<?php

namespace Infrastructure\Oauth2Server\Bridge;

use Trailmind\Access\AccessTokenRepository;
use Trailmind\Access\Exception\RefreshTokenNotFoundException;
use Trailmind\Access\RefreshToken as TrailmindRefreshToken;
use Trailmind\Access\RefreshTokenRepository;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class OauthRefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private AccessTokenRepository $accessTokenRepository,
        private RefreshTokenRepository $refreshTokenRepository
    ) {}
    
    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshToken();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $id = $refreshTokenEntity->getIdentifier();
        $accessTokenId = $refreshTokenEntity->getAccessToken()->getIdentifier();
        $accessToken = $this->accessTokenRepository->findOneById($accessTokenId);
        $expiryDateTime = $refreshTokenEntity->getExpiryDateTime();
        
        $refreshToken = new TrailmindRefreshToken($id, $accessToken, $expiryDateTime);
        $this->refreshTokenRepository->save($refreshToken);
    }
    
    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId): void
    {
        try {
            $refreshToken = $this->refreshTokenRepository->findOneById($tokenId);
            $refreshToken->revoke();
            $this->refreshTokenRepository->save($refreshToken);
        } catch (RefreshTokenNotFoundException $rtnfe) {
            //going to leave this for now as handled
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        try {
            $refreshToken = $this->refreshTokenRepository->findOneById($tokenId);
            if ($refreshToken->isRevoked()) {
                return true;
            }
            return $refreshToken->getAccessToken()->isRevoked();
        } catch (RefreshTokenNotFoundException $rtnfe) {
            return true;
        }
    }
}

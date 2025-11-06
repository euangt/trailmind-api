<?php

namespace Infrastructure\Oauth2Server\Bridge;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Trailmind\Access\AccessToken as TrailmindAccessToken;
use Trailmind\Access\AccessTokenRepository;
use Trailmind\Access\ClientRepository;
use Trailmind\Access\Exception\AccessTokenNotFoundException;
use Trailmind\User\UserRepository;

class OauthAccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function __construct(
        private AccessTokenRepository $accessTokenRepository,
        private UserRepository $userRepository,
        private ClientRepository $clientRepository
    ) {}

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
    {
        $accessToken = new AccessToken($userIdentifier, $scopes);
        $accessToken->setClient($clientEntity);

        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $user = $this->userRepository->findOneById($accessTokenEntity->getUserIdentifier());

        $client = $this->clientRepository->findActiveById($accessTokenEntity->getClient()->getIdentifier());

        $accessToken = new TrailmindAccessToken(
            $accessTokenEntity->getIdentifier(),
            $user,
            $client,
            $this->scopesToArray($accessTokenEntity->getScopes()),
            new \DateTime(),
            new \DateTime(),
            $accessTokenEntity->getExpiryDateTime()
        );
        $this->accessTokenRepository->save($accessToken);
    }

    private function scopesToArray(array $scopes): array
    {
        return array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $scopes);
    }

    public function revokeAccessToken($tokenId): void
    {
        try {
            $accessToken = $this->accessTokenRepository->findOneById($tokenId);

            $accessToken->revoke();
            $this->accessTokenRepository->save($accessToken);
        } catch (AccessTokenNotFoundException $atnfe) {
            //For now I am going to do nothing here
        }
    }

    public function isAccessTokenRevoked(string $tokenId): bool
    {
        try {
            $accessToken = $this->accessTokenRepository->findOneById($tokenId);

            return $accessToken->isRevoked();
        } catch (AccessTokenNotFoundException $atnfe) {
            return true;
        }
    }
}

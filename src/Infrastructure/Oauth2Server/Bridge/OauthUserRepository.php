<?php

namespace Infrastructure\Oauth2Server\Bridge;

use Trailmind\User\Exception\UserNotFoundException;
use Trailmind\User\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class OauthUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        try {
            $user = $this->userRepository->findOneByEmail($username);
        } catch (UserNotFoundException $unfe) {
            return null;
        }
        
        return new User($user->getId());
    }
}

<?php

namespace Infrastructure\Oauth2Server\Bridge;

use Trailmind\Access\ClientRepository;
use Trailmind\Access\Exception\ClientNotFoundException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class OauthClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = false
    ): ?ClientEntityInterface {
        try {
            $appClient = $this->clientRepository->findActiveById($clientIdentifier);
        } catch (ClientNotFoundException $cnfe) {
            return null;
        }

        //The calling class used to provide values for secret, and mustValidateSecret
        //but it appears not to any more.  don't know why, and need to investigate
        if ($mustValidateSecret && !hash_equals($appClient->getSecret(), (string)$clientSecret)) {
            return null;
        }
        return new Client($clientIdentifier, $appClient->getName(), $appClient->getRedirect());
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        return true;
    }
}

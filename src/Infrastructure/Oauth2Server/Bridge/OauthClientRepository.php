<?php

namespace Infrastructure\Oauth2Server\Bridge;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Trailmind\Access\ClientRepository;
use Trailmind\Access\Exception\ClientNotFoundException;

class OauthClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private ClientRepository $clientRepository
    ) {}

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
        if ($mustValidateSecret && ! hash_equals($appClient->getSecret(), (string) $clientSecret)) {
            return null;
        }
        return new Client($clientIdentifier, $appClient->getName(), $appClient->getRedirect());
    }

    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        try {
            $client = $this->clientRepository->findActiveById($clientIdentifier);
            return hash_equals($client->getSecret(), (string) $clientSecret);
        } catch (ClientNotFoundException $e) {
            return false;
        }
    }
}

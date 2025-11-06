<?php

namespace Trailmind\Access;

use Trailmind\Access\Exception\ClientNotFoundException;

interface ClientRepository
{
    /**
     * @throws ClientNotFoundException
     */
    public function findActiveById(string $id): ?Client;

    /**
     * @throws ClientNotFoundException
     */
    public function findOneByIdAndSecret(string $id, string $secret): ?Client;

    public function save(Client $client): void;
}

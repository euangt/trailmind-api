<?php

namespace Trailmind\Access;

use Trailmind\Access\Exception\ClientNotFoundException;

interface ClientRepository
{
    /**
     * @param string $id
     *
     * @return Client
     *
     * @throws ClientNotFoundException
     */
    public function findActiveById(string $id): ?Client;

    /**
     * @param string $id
     * @param string $secret
     *
     * @return Client
     *
     * @throws ClientNotFoundException
     */
    public function findOneByIdAndSecret(string $id, string $secret): ?Client;

    /**
     * @param Client $client
     */
    public function save(Client $client): void;
}

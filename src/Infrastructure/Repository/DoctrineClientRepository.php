<?php

namespace Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Trailmind\Access\Client;
use Trailmind\Access\ClientRepository;
use Trailmind\Access\Exception\ClientNotFoundException;

final class DoctrineClientRepository implements ClientRepository
{
    private const ENTITY = Client::class;

    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->repository = $this->entityManager->getRepository(self::ENTITY);
    }

    public function findActiveById(string $id): ?Client
    {
        try {
            $client = $this->repository->findOneBy([
                'id' => $id,
                'active' => true,
            ]);
        } catch (ConversionException $ce) {
            $client = null;
        }

        if (is_null($client)) {
            throw new ClientNotFoundException();
        }

        return $client;
    }

    public function findOneByIdAndSecret(string $id, string $secret): ?Client
    {
        $client = $this->repository->findOneBy([
            'id' => $id,
            'secret' => $secret,
        ]);

        if (is_null($client)) {
            throw new ClientNotFoundException();
        }

        return $client;
    }

    public function save(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }
}

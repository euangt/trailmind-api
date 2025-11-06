<?php

namespace Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Trailmind\Access\AccessToken;
use Trailmind\Access\AccessTokenRepository;
use Trailmind\Access\Exception\AccessTokenNotFoundException;
use Trailmind\User\User;

final class DoctrineAccessTokenRepository implements AccessTokenRepository
{
    private const ENTITY = AccessToken::class;

    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(self::ENTITY);
    }

    public function findOneById(string $id): ?AccessToken
    {
        try {
            $accessToken = $this->repository->findOneBy([
                'id' => $id,
            ]);
        } catch (ConversionException $ce) {
            $accessToken = null;
        }

        if (is_null($accessToken)) {
            throw new AccessTokenNotFoundException();
        }

        return $accessToken;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findUnrevokedByUser(User $user): ?AccessToken
    {
        $accessToken = $this->repository->findOneBy([
            'user' => $user,
            'revoked' => false,
        ]);

        if (is_null($accessToken)) {
            throw new AccessTokenNotFoundException();
        }

        return $accessToken;
    }

    public function save(AccessToken $accessToken): void
    {
        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();
    }
}

<?php

namespace Infrastructure\Repository;

use Doctrine\Persistence\ObjectRepository;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Trailmind\Access\AccessToken;
use Trailmind\Access\Exception\RefreshTokenNotFoundException;
use Trailmind\Access\RefreshToken;
use Trailmind\Access\RefreshTokenRepository;

final class DoctrineRefreshTokenRepository implements RefreshTokenRepository
{
    private const ENTITY = RefreshToken::class;
    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->repository = $this->entityManager->getRepository(self::ENTITY);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById(string $id): ?RefreshToken
    {
        try {
            $refreshToken = $this->repository->findOneBy(['id' => $id]);
        } catch (ConversionException $ce) {
            $refreshToken = null;
        }

        if (is_null($refreshToken)) {
            throw new RefreshTokenNotFoundException();
        }

        return $refreshToken;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByAccessToken(AccessToken $accessToken): ?RefreshToken
    {
        $refreshToken = $this->repository->findOneBy(['accessToken' => $accessToken]);

        if (is_null($refreshToken)) {
            throw new RefreshTokenNotFoundException();
        }

        return $refreshToken;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function save(RefreshToken $refreshToken): void
    {
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();
    }
}

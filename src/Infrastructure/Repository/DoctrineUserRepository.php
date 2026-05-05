<?php

namespace Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Trailmind\Access\AccessToken;
use Trailmind\User\Exception\UserNotFoundException;
use Trailmind\User\User;
use Trailmind\User\UserRepository;

class DoctrineUserRepository implements UserRepository
{
    private const ENTITY = User::class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        $this->repository = $this->entityManager->getRepository(self::ENTITY);
    }

    public function findOneById(string $id): User
    {
        try {
            $user = $this->repository->findOneBy([
                'id' => $id,
            ]);
        } catch (ConversionException $ce) {
            $user = null;
        }

        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findOneByEmail(string $email): User
    {
        try {
            $user = $this->repository->findOneBy([
                'email' => $email,
            ]);
        } catch (ConversionException $ce) {
            $user = null;
        }

        if (is_null($user)) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function findOneByAccessToken(string $accessToken): User
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $user = $queryBuilder->select('u')
            ->from(self::ENTITY, 'u')
            ->from(AccessToken::class, 'a')
            ->where('a.user = u')
            ->andWhere('a.id = :accessToken')
            ->andWhere('a.revoked = false')
            ->andWhere('a.expiresAt > :now')
            ->setParameter('accessToken', $accessToken)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}

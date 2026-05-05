<?php

namespace Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Trailmind\Hike\Exception\HikeNotFoundException;
use Trailmind\Hike\Hike;
use Trailmind\Hike\HikeRepository;
use Trailmind\User\User;

class DoctrineHikeRepository implements HikeRepository
{
    private const ENTITY = Hike::class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    public function __construct(
        protected EntityManagerInterface $entityManager
    ) {
        $this->repository = $this->entityManager->getRepository(self::ENTITY);
    }

    public function findOneById(string $id): Hike
    {
        try {
            $hike = $this->repository->findOneBy([
                'id' => $id,
            ]);
        } catch (ConversionException $ce) {
            $hike = null;
        }

        if (is_null($hike)) {
            throw new HikeNotFoundException();
        }

        return $hike;
    }

    public function save(Hike $hike): void
    {
        $this->entityManager->persist($hike);
        $this->entityManager->flush();
    }

    public function findAllByUser(User $user): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder = $queryBuilder->select('h')
            ->from(self::ENTITY, 'h')
            ->where('h.user = :user')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }
}
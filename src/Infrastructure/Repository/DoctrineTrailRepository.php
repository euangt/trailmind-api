<?php

namespace Infrastructure\Repository;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Trailmind\Trail\Exception\TrailNotFoundException;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailRepository;

class DoctrineTrailRepository implements TrailRepository
{
    private const ENTITY = Trail::class;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->repository = $this->entityManager->getRepository(self::ENTITY);
    }

    public function findOneById(string $id): Trail
    {
        try {
            $trail = $this->repository->findOneBy([
                'id' => $id,
            ]);
        } catch (ConversionException $ce) {
            $trail = null;
        }

        if (is_null($trail)) {
            throw new TrailNotFoundException();
        }

        return $trail;
    }

    public function save(Trail $trail): void
    {
        $this->entityManager->persist($trail);
        $this->entityManager->flush();
    }
}
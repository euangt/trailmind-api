<?php

namespace TestingInfrastructure\Repositories;

use Doctrine\DBAL\Types\ConversionException;
use Infrastructure\Repository\DoctrineTrailRepository;
use Trailmind\Trail\Exception\TrailNotFoundException;
use Trailmind\Trail\Trail;

class BehatDoctrineTrailRepository extends DoctrineTrailRepository
{
    /**
     * In practice we cannot guarantee a trail name is unique, but for testing purposes it is fine
     * 
     * @param string $name
     * @return Trail
     */
    public function findOneByName(string $name): Trail
    {
        try {
            $trail = $this->repository->findOneBy(['name' => $name]);
        } catch (ConversionException $ce) {
            $trail = null;
        }

        if (is_null($trail)) {
            throw new TrailNotFoundException();
        }

        return $trail;
    }

    /**
     * In practice we cannot guarantee a trail name is unique, but for testing purposes it is fine
     * 
     * @param string $name
     * @return Trail
     */
    public function findOneByNameUncached(string $name): Trail
    {
        $this->entityManager->clear();

        return $this->findOneByName($name);
    }
}
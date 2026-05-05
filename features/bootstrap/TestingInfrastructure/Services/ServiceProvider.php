<?php

namespace TestingInfrastructure\Services;

use Symfony\Component\HttpKernel\KernelInterface;
use Trailmind\Hike\HikeRepository;
use Trailmind\Trail\TrailRepository;
use Trailmind\User\UserRepository;

class ServiceProvider
{
    public function __construct(
        private KernelInterface $kernel,
    ) {}

    public function getTrailRepository(): TrailRepository
    {
        return $this->kernel->getContainer()->get('Trailmind\Trail\TrailRepository');
    }

    public function getUserRepository(): UserRepository
    {
        return $this->kernel->getContainer()->get('Trailmind\User\UserRepository');
    }

    public function getHikeRepository(): HikeRepository
    {
        return $this->kernel->getContainer()->get('Trailmind\Hike\HikeRepository');
    }
}

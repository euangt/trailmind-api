<?php

namespace TestingInfrastructure\Services;

use Symfony\Component\HttpKernel\KernelInterface;
use Trailmind\Trail\TrailRepository;

class ServiceProvider
{
    /**
     * @param KernelInterface $kernel
     */
    public function __construct(
        private KernelInterface $kernel)
    {}

    /**
     * @return TrailRepository
     */
    public function getTrailRepository()
    {
        return $this->kernel->getContainer()->get('Trailmind\Trail\TrailRepository');
    }
}
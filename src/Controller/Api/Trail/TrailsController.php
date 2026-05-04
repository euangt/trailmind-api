<?php

namespace Controller\Api\Trail;

use Dto\Outbound\Success;
use Dto\Outbound\Trail\TrailCollectionBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\Trail\TrailRepository;

class TrailsController
{
    public function __construct(
        private readonly TrailRepository $trailRepository,
        private readonly TrailCollectionBuilder $trailCollectionBuilder,
    ) {}

    #[Route('/v1.0/trails', methods: ['GET'], name: 'api_v1.0_view_trails')]
    public function getTrailAction(): Success {
        return $this->trailCollectionBuilder
            ->setContext('v1.0_view_trails')
            ->build($this->trailRepository->findAll());
    }
}
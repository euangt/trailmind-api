<?php

namespace Controller\Trail;

use Application\ValueResolver\CustomisableValueResolver;
use Dto\Outbound\Success;
use Dto\Outbound\Trail\TrailBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\Trail\Trail;

class TrailController
{
    public function __construct(
        private readonly TrailBuilder $trailBuilder,
    ) {}

    #[Route('/v1.0/trail/{trail_id}', methods: ['GET'], name: 'api_v1.0_view_trail')]
    public function getTrailAction(
        #[CustomisableValueResolver('entity', false, ['class' => Trail::class, 'mapping' => ['trail_id' => 'id']])] $trail,
    ): Success {
        return $this->trailBuilder
                ->setContext('v1.0_view_trail')
                ->build($trail);
    }
}
<?php

namespace Controller\Api\Hike;

use Application\ValueResolver\CustomisableValueResolver;
use Dto\Outbound\Hike\HikeCollectionBuilder;
use Dto\Outbound\Success;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Trailmind\Hike\HikeRepository;
use Trailmind\User\User;

class HikesController
{
    public function __construct(
        private readonly HikeRepository $hikeRepository,
        private readonly HikeCollectionBuilder $hikeCollectionBuilder,
    ) {}

    #[IsGranted('ROLE_USER')]
    #[Route('/v1.0/hikes', methods: ['GET'], name: 'api_v1.0_view_hikes')]
    public function getHikesAction(
        #[CustomisableValueResolver('authenticated_user')]
        User $user,
    ): Success {
        return $this->hikeCollectionBuilder
            ->setContext('v1.0_view_hikes')
            ->build($this->hikeRepository->findAllByUser($user));
    }
}

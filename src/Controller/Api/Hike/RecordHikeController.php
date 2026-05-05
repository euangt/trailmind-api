<?php

namespace Controller\Api\Hike;

use Application\Dto\Inbound\Hike\RecordingHike;
use Application\ValueResolver\CustomisableValueResolver;
use DateTimeImmutable;
use Dto\Outbound\EntityDto;
use Dto\Outbound\Hike\HikeBuilder;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Trailmind\Hike\Hike;
use Trailmind\Hike\HikeRepository;
use Trailmind\Trail\Exception\TrailNotFoundException;
use Trailmind\Trail\TrailRepository;
use Trailmind\User\User;

class RecordHikeController
{
    public function __construct(
        private readonly HikeRepository $hikeRepository,
        private readonly TrailRepository $trailRepository,
        private readonly HikeBuilder $hikeBuilder,
    ) {}

    #[IsGranted('ROLE_USER')]
    #[Route('/v1.0/hike', methods: ['POST'], name: 'api_v1.0_record_hike')]
    public function postRecordHikeAction(
        #[MapRequestPayload(acceptFormat: 'json')]
        RecordingHike $recordingHike,
        #[CustomisableValueResolver('authenticated_user')]
        User $user,
    ): EntityDto {
        try {
            $trail = $this->trailRepository->findOneById($recordingHike->trailId);
        } catch (TrailNotFoundException) {
            throw new NotFoundHttpException('Trail not found');
        }

        try {
            $startDate = new DateTimeImmutable($recordingHike->startDate);
            $endDate   = new DateTimeImmutable($recordingHike->endDate);
        } catch (\Exception) {
            throw new UnprocessableEntityHttpException('startDate and endDate must be valid date-time strings');
        }

        if ($endDate <= $startDate) {
            throw new UnprocessableEntityHttpException('endDate must be after startDate');
        }

        $hike = new Hike($trail, $user, $startDate, $endDate);

        $this->hikeRepository->save($hike);

        return $this->hikeBuilder
            ->setContext('v1.0_record_hike')
            ->build($hike);
    }
}

<?php

namespace Application\Dto\Inbound\Hike;

use Symfony\Component\Validator\Constraints as Assert;

class RecordingHike
{
    public function __construct(
        #[Assert\NotBlank]
        public string $trailId,
        #[Assert\NotBlank]
        public string $startDate,
        #[Assert\NotBlank]
        public string $endDate,
    ) {}
}

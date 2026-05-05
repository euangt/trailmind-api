<?php

namespace Dto\Outbound\Hike;

use Dto\Outbound\Created;
use Dto\Outbound\EntityBuilder;
use Trailmind\Hike\Hike;

class HikeBuilder extends EntityBuilder
{
    protected const EXPECTED_CLASS = Hike::class;

    private Hike $hike;

    protected function initialise($initialisable): EntityBuilder
    {
        $this->hike = $initialisable;

        $this->dto = new Created();
        $this->dto->add('id', $this->hike->getId());
        $this->dto->add('startDate', $this->hike->getStartDate()->format('Y-m-d H:i:s'));
        $this->dto->add('endDate', $this->hike->getEndDate()->format('Y-m-d H:i:s'));

        return $this;
    }
}

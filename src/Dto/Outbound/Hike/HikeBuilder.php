<?php

namespace Dto\Outbound\Hike;

use Dto\Outbound\Created;
use Dto\Outbound\EntityBuilder;
use Dto\Outbound\Hike\Decorators\TrailDecorator;
use Trailmind\Hike\Hike;

class HikeBuilder extends EntityBuilder
{
    protected const EXPECTED_CLASS = Hike::class;

    private Hike $hike;

    public function __construct(TrailDecorator $trailDecorator)
    {
        $this->decorators = [$trailDecorator];
    }

    protected function initialise($initialisable): EntityBuilder
    {
        $this->hike = $initialisable;

        $this->dto = new Created();
        $this->dto->add('id', $this->hike->getId());
        $this->dto->add('startDate', $this->hike->getStartDate()->format('Y-m-d H:i:s'));
        $this->dto->add('endDate', $this->hike->getEndDate()->format('Y-m-d H:i:s'));

        return $this;
    }

    protected function decorate(): self
    {
        foreach ($this->decorators as $decorator) {
            $decorator->withHike($this->hike)
                ->decorate($this->dto, $this->context);
        }

        return $this;
    }
}

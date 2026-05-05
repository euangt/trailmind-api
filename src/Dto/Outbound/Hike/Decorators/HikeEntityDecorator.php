<?php

namespace Dto\Outbound\Hike\Decorators;

use Dto\Outbound\EntityDecorator;
use Trailmind\Hike\Hike;

abstract class HikeEntityDecorator extends EntityDecorator
{
    protected Hike $hike;

    public function withHike(Hike $hike): self
    {
        $this->hike = $hike;

        return $this;
    }
}

<?php

namespace Dto\Outbound\Trail\Decorators;

use Dto\Outbound\EntityDecorator;
use Trailmind\Trail\Trail;

abstract class TrailEntityDecorator extends EntityDecorator
{
    /**
     * @var Trail
     */
    protected $trail;

    public function withTrail(Trail $trail): self
    {
        $this->trail = $trail;

        return $this;
    }
}
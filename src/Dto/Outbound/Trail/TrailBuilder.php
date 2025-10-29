<?php

namespace Dto\Outbound\Trail;

use Dto\Outbound\EntityBuilder;
use Dto\Outbound\Success;
use Trailmind\Trail\Trail;

class TrailBuilder extends EntityBuilder
{
    protected const EXPECTED_CLASS = Trail::class;
    protected const COLLECTION_NAME = 'trails';

    /**
     * @var Trail
     */
    private Trail $trail;

    /**
     * @inheritdoc
     */
    protected function initialise($initialisable): EntityBuilder
    {
        // For readability
        $this->trail = $initialisable;

        $this->dto = new Success();
        $this->dto->add('id', $this->trail->getId());
        $this->dto->add('name', $this->trail->getName());

        return $this;
    }
}
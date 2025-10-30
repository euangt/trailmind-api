<?php

namespace Dto\Outbound\Trail;

use Dto\Outbound\EntityBuilder;
use Dto\Outbound\Success;
use Dto\Outbound\Trail\Decorators\DifficultyDecorator;
use Dto\Outbound\Trail\Decorators\LengthDecorator;
use Trailmind\Trail\Trail;

class TrailBuilder extends EntityBuilder
{
    protected const EXPECTED_CLASS = Trail::class;
    protected const COLLECTION_NAME = 'trails';

    /**
     * @var Trail
     */
    private Trail $trail;

    public function __construct(
        DifficultyDecorator $difficultyDecorator,
        LengthDecorator $lengthDecorator
    ) {
        $this->decorators = [
            $difficultyDecorator,
            $lengthDecorator
        ];
    }

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

    /**
     * @inheritdoc
     */
    protected function decorate(): self
    {
        foreach ($this->decorators as $decorator) {
            $decorator->withTrail($this->trail)
                    ->decorate($this->dto, $this->context);
        }

        return $this;
    }
}
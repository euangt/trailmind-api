<?php

namespace Dto\Outbound\Trail\Decorators;

use Dto\Outbound\EntityDto;

class DifficultyDecorator extends TrailEntityDecorator
{
    protected const CONTEXTS = [
        'v1.0_view_trail'
    ];

    /**
     * {@inheritDoc}
     */
    public function decorate(
        EntityDto $entityDto, 
        string $context
    ): EntityDto
    {
        if ($this->shouldDecorate($context)) {
            $entityDto->add('difficulty', $this->trail->getDifficulty());
        }

        return $entityDto;
    }
}
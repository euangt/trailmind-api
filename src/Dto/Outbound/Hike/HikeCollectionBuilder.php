<?php

namespace Dto\Outbound\Hike;

use Dto\Outbound\CollectionBuilder;
use Dto\Outbound\Success;

class HikeCollectionBuilder extends CollectionBuilder
{
    public function __construct(
        private HikeBuilder $hikeBuilder,
    ) {}

    protected function initialise($initialisable): self
    {
        $this->dto = new Success();

        $entities = [];
        foreach ($initialisable as $hike) {
            $entities[] = $this->hikeBuilder
                ->setContext($this->context)
                ->build($hike);
        }

        $this->dto->add('hikes', $entities);

        return $this;
    }
}

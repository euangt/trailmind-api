<?php

namespace Dto\Outbound\Trail;

use Dto\Outbound\CollectionBuilder;
use Dto\Outbound\Success;

class TrailCollectionBuilder extends CollectionBuilder
{
    /**
     * @param TrailBuilder $trailBuilder
     */
    public function __construct(
        private TrailBuilder $trailBuilder
    ) {}

    /**
     * @param mixed $initialisable
     *
     * @return self
     */
    protected function initialise($initialisable): self
    {
        $trails = $initialisable;
        $this->dto = new Success();

        $entities = [];
        foreach ($trails as $trail) {
            $entities[] = $this->trailBuilder
                                ->setContext($this->context)
                                ->build($trail);
        }

        $this->dto->add('trails', $entities);

        return $this;
    }
}
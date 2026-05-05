<?php

namespace Dto\Outbound\Hike\Decorators;

use Dto\Outbound\EntityDto;

class TrailDecorator extends HikeEntityDecorator
{
    protected const CONTEXTS = [
        'v1.0_record_hike',
        'v1.0_view_hikes',
    ];

    public function decorate(EntityDto $entityDto, string $context): EntityDto
    {
        if ($this->shouldDecorate($context)) {
            $trail = $this->hike->getTrail();
            $entityDto->add('trail', [
                'id'   => $trail->getId(),
                'name' => $trail->getName(),
            ]);
        }

        return $entityDto;
    }
}

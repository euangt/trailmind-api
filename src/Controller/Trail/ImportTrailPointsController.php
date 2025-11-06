<?php

namespace Controller\Trail;

use Application\ValueResolver\CustomisableValueResolver;
use Dto\Inbound\File\Filename;
use Dto\Outbound\Created;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\Trail\Trail;

class ImportTrailPointsController
{
    #[Route('/v1.0/trail/{trail_id}/import-trail-points', methods: ['POST'], name: 'api_v1.0_import_trail_points')]
    public function importTrailPointsAction(
        #[CustomisableValueResolver('entity', false, [
            'class' => Trail::class,
            'mapping' => [
                'trail_id' => 'id',
            ],
        ])]
        $trail,
        #[MapRequestPayload(acceptFormat: 'json')]
        Filename $filename
    ): Created
    {
        return new Created();
    }
}
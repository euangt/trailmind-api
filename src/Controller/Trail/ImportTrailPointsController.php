<?php

namespace Controller\Trail;

use Application\ValueResolver\CustomisableValueResolver;
use Dto\Inbound\File\Filename;
use Dto\Outbound\Created;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\Trail\Trail;
use Trailmind\TrailService\TrailPointManager\TrailPointImporter\TrailPointsImporter;
use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;

class ImportTrailPointsController
{
    public function __construct(
        private TrailPointLoader $trailPointLoader,
        private TrailPointsImporter $trailPointsImporter,
    ) {}

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
        try {
            $file = $this->trailPointLoader->loadFile($filename->filename);
        } catch (FileNotFoundException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $this->trailPointsImporter->importFile($trail, $file);

        return new Created();
    }
}
<?php

namespace Trailmind\TrailService\TrailPointManager\TrailPointImporter;

use SimpleXMLElement;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailPoint;
use Trailmind\Trail\TrailRepository;

class TrailPointsImporter
{
    public function __construct(
        private TrailRepository $trailRepository
    ) {}

    public function importFile(
        Trail $trail,
        SimpleXMLElement $file
    ): bool {
        $namespaces = $file->getNamespaces(true);
        $trk = $file->trk;

        $points = [];
        $sequence = 0;
        foreach ($trk->trkseg as $trkseg) {
            foreach ($trkseg->trkpt as $trkpt) {
                // There will be floating point inprecision here but the GPX impact will be negligible
                $lat = (float) $trkpt['lat'];
                $lon = (float) $trkpt['lon'];
                $ele = isset($trkpt->ele) ? (float) $trkpt->ele : null;

                $trailPoint = new TrailPoint(
                    $trail,
                    $lat,
                    $lon,
                    $ele,
                    $sequence++
                );

                $trailPoint->setGeom(sprintf('POINT(%f %f)', $lon, $lat));

                $points[] = $trailPoint;
            }
        }

        $trail->setTrailPoints($points);

        if (!empty($points)) {
            $coordinates = array_map(
                fn($point) => sprintf('%f %f', $point->getLongitude(), $point->getLatitude()),
                $points
            );
            $trail->setRoute('LINESTRING(' . implode(',', $coordinates) . ')');
            $trail->setStartPoint($points[0]);
            $trail->setEndPoint(end($points));
        }

        $this->trailRepository->save($trail);

        return true;
    }
}
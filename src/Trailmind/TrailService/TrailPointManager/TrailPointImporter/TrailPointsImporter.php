<?php

namespace Trailmind\TrailService\TrailPointManager\TrailPointImporter;

use Trailmind\Trail\Trail;

interface TrailPointsImporter
{
    public function importFile(Trail $trail, mixed $file): bool;

    public function supportsFile(mixed $file): bool;
}
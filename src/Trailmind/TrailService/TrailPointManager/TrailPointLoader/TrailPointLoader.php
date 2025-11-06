<?php

namespace Trailmind\TrailService\TrailPointManager\TrailPointLoader;

use Trailmind\FileService\FileLoader;

abstract class TrailPointLoader extends FileLoader
{
    protected const SUPPORTED_FILE_TYPE = 'gpx';
}
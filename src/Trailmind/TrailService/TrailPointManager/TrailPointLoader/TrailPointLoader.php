<?php

namespace Trailmind\TrailService\TrailPointManager\TrailPointLoader;

use Trailmind\FileService\FileLoader;

abstract class TrailPointLoader extends FileLoader
{
    static protected const SUPPORTED_FILE_TYPE = 'gpx';
}
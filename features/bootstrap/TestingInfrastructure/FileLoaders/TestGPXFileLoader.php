<?php

namespace TestingInfrastructure\FileLoaders;

use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class TestGPXFileLoader extends TrailPointLoader
{
    const UPLOAD_DIRECTORIES = [
        'trails/'
    ];

    public function __construct(
        private string $projectDir
    ) {}

    public function loadFile(string $filename): mixed
    {
        $found = false;
        foreach (self::UPLOAD_DIRECTORIES as $directory) {
            $path = $this->projectDir . "/fixtures/" . $directory . $filename;
            if (file_exists($path)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new FileNotFoundException("File not found for filename: {$filename}");
        }

        $this->validateFileType($path);

        return simplexml_load_file($path);
    }
}
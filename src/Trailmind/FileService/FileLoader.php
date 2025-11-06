<?php

namespace Trailmind\FileService;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Trailmind\FileService\Exception\UnableToLoadFileException;

abstract class FileLoader
{
    protected const SUPPORTED_FILE_TYPE = '';

    /**
     * @throws FileNotFoundException
     */
    abstract public function loadFile(string $filePath): mixed;

    protected function validateFileType(mixed $file): void
    {
        $fileType = pathinfo($file, PATHINFO_EXTENSION);
        if ($fileType !== static::SUPPORTED_FILE_TYPE) {
            throw new UnableToLoadFileException("File type " . $fileType . " is not supported by " . static::class);
        }
    } 
}
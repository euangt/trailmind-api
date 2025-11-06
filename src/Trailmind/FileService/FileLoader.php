<?php

namespace Trailmind\FileService;

use Trailmind\FileService\Exception\UnableToLoadFileException;

abstract class FileLoader
{
    static protected const SUPPORTED_FILE_TYPE = '';

    abstract public function loadFile(string $filePath): void;

    protected function validateFileType(mixed $file): void
    {
        $fileType = pathinfo($file, PATHINFO_EXTENSION);
        if ($fileType !== static::SUPPORTED_FILE_TYPE) {
            throw new UnableToLoadFileException("File type " . $fileType . " is not supported by " . static::class);
        }
    } 
}
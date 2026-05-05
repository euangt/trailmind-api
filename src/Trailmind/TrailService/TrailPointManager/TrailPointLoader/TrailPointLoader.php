<?php

namespace Trailmind\TrailService\TrailPointManager\TrailPointLoader;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;
use Trailmind\FileService\Exception\UnableToLoadFileException;
use Trailmind\FileService\FileLoader;

class TrailPointLoader extends FileLoader
{
    protected const SUPPORTED_FILE_TYPE = 'gpx';

    public function __construct(
        private KernelInterface $kernel,
    ) {}

    public function loadFile(string $filePath): mixed
    {
        $resolvedPath = $this->resolvePath($filePath);

        $this->validateFileType($resolvedPath);

        $previousUseInternalErrors = libxml_use_internal_errors(true);

        try {
            $file = simplexml_load_file($resolvedPath);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousUseInternalErrors);
        }

        if ($file === false) {
            throw new UnableToLoadFileException(sprintf('Unable to parse GPX file: %s', $filePath));
        }

        return $file;
    }

    private function resolvePath(string $filePath): string
    {
        $candidatePaths = [$filePath];

        if (! str_starts_with($filePath, DIRECTORY_SEPARATOR)) {
            $relativePath = ltrim($filePath, DIRECTORY_SEPARATOR);
            $projectDir = $this->kernel->getProjectDir();

            $candidatePaths[] = $projectDir . DIRECTORY_SEPARATOR . $relativePath;
            $candidatePaths[] = $projectDir . DIRECTORY_SEPARATOR . 'fixtures/trails/' . $relativePath;
        }

        foreach ($candidatePaths as $candidatePath) {
            if (file_exists($candidatePath)) {
                return $candidatePath;
            }
        }

        throw new FileNotFoundException(sprintf('File not found: %s', $filePath));
    }
}

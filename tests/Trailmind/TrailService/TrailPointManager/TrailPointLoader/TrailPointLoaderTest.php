<?php

namespace App\Tests\Trailmind\TrailService\TrailPointManager\TrailPointLoader;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;
use Trailmind\FileService\Exception\UnableToLoadFileException;
use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;

final class TrailPointLoaderTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $temporaryDirectories = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryDirectories as $directory) {
            $this->removeDirectory($directory);
        }

        $this->temporaryDirectories = [];
    }

    public function testLoadFileLoadsGpxFromProjectRelativePath(): void
    {
        $projectDirectory = $this->createProjectDirectory();
        $this->writeFile($projectDirectory, 'imports/example.gpx', $this->validGpx('Project Relative Track'));

        $loader = new TrailPointLoader($this->createKernel($projectDirectory));
        $file = $loader->loadFile('imports/example.gpx');

        self::assertInstanceOf(SimpleXMLElement::class, $file);
        self::assertSame('Project Relative Track', (string) $file->trk->name);
    }

    public function testLoadFileFallsBackToFixturesTrailDirectory(): void
    {
        $projectDirectory = $this->createProjectDirectory();
        $this->writeFile($projectDirectory, 'fixtures/trails/example.gpx', $this->validGpx('Fixture Track'));

        $loader = new TrailPointLoader($this->createKernel($projectDirectory));
        $file = $loader->loadFile('example.gpx');

        self::assertInstanceOf(SimpleXMLElement::class, $file);
        self::assertSame('Fixture Track', (string) $file->trk->name);
    }

    public function testLoadFileThrowsWhenFileDoesNotExist(): void
    {
        $projectDirectory = $this->createProjectDirectory();
        $loader = new TrailPointLoader($this->createKernel($projectDirectory));

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File not found: missing.gpx');

        $loader->loadFile('missing.gpx');
    }

    public function testLoadFileRejectsUnsupportedFileTypes(): void
    {
        $projectDirectory = $this->createProjectDirectory();
        $this->writeFile($projectDirectory, 'imports/example.xml', $this->validGpx('Wrong Extension Track'));

        $loader = new TrailPointLoader($this->createKernel($projectDirectory));

        $this->expectException(UnableToLoadFileException::class);
        $this->expectExceptionMessage('File type xml is not supported');

        $loader->loadFile('imports/example.xml');
    }

    public function testLoadFileRejectsInvalidGpxContent(): void
    {
        $projectDirectory = $this->createProjectDirectory();
        $this->writeFile($projectDirectory, 'imports/broken.gpx', '<gpx><trk><name>Broken</name>');

        $loader = new TrailPointLoader($this->createKernel($projectDirectory));

        $this->expectException(UnableToLoadFileException::class);
        $this->expectExceptionMessage('Unable to parse GPX file: imports/broken.gpx');

        $loader->loadFile('imports/broken.gpx');
    }

    private function createKernel(string $projectDirectory): KernelInterface
    {
        $kernel = $this->createMock(KernelInterface::class);
        $kernel->method('getProjectDir')->willReturn($projectDirectory);

        return $kernel;
    }

    private function createProjectDirectory(): string
    {
        $directory = sys_get_temp_dir() . '/trailmind-loader-' . bin2hex(random_bytes(8));

        mkdir($directory, 0777, true);
        $this->temporaryDirectories[] = $directory;

        return $directory;
    }

    private function writeFile(string $projectDirectory, string $relativePath, string $contents): void
    {
        $fullPath = $projectDirectory . DIRECTORY_SEPARATOR . $relativePath;
        $parentDirectory = dirname($fullPath);

        if (! is_dir($parentDirectory)) {
            mkdir($parentDirectory, 0777, true);
        }

        file_put_contents($fullPath, $contents);
    }

    private function validGpx(string $trackName): string
    {
        return sprintf(
            '<?xml version="1.0" encoding="UTF-8"?><gpx version="1.1" creator="Trail Loader Test" xmlns="http://www.topografix.com/GPX/1/1"><trk><name>%s</name><trkseg><trkpt lat="51.632539546" lon="-2.648426341"><ele>20.43</ele></trkpt></trkseg></trk></gpx>',
            $trackName,
        );
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $entries = scandir($directory);

        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($path)) {
                $this->removeDirectory($path);

                continue;
            }

            unlink($path);
        }

        rmdir($directory);
    }
}

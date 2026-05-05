<?php

namespace Application\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Trailmind\FileService\Exception\UnableToLoadFileException;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailRepository;
use Trailmind\Trail\Exception\TrailNotFoundException;
use Trailmind\TrailService\TrailPointManager\TrailPointImporter\TrailPointsImporter;
use Trailmind\TrailService\TrailPointManager\TrailPointLoader\TrailPointLoader;

#[AsCommand(
    name: 'trailmind:trail:import-gpx',
    description: 'Import GPX trail points into an existing trail or create a new trail from the GPX name.'
)]
class ImportTrailPointsCommand extends Command
{
    private const DEFAULT_DIFFICULTY = 'Moderate';
    private const DEFAULT_LENGTH = 0.0;

    public function __construct(
        private TrailRepository $trailRepository,
        private TrailPointLoader $trailPointLoader,
        private TrailPointsImporter $trailPointsImporter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file-path', InputArgument::REQUIRED, 'Absolute path or project-relative path to a GPX file.')
            ->addArgument('trail-id', InputArgument::OPTIONAL, 'Existing trail ID to import points into. Omit to create a new trail from the GPX track name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = (string) $input->getArgument('file-path');
        $trailId = $input->getArgument('trail-id');

        try {
            $file = $this->trailPointLoader->loadFile($filePath);
        } catch (FileNotFoundException | UnableToLoadFileException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        if (! $this->trailPointsImporter->supportsFile($file)) {
            $io->error(sprintf('File "%s" is not a supported GPX track file.', $filePath));

            return Command::FAILURE;
        }

        $creatingTrail = ! is_string($trailId) || trim($trailId) === '';

        try {
            $trail = $creatingTrail
                ? $this->createTrailFromFile($file, $filePath)
                : $this->trailRepository->findOneById($trailId);
        } catch (TrailNotFoundException) {
            $io->error(sprintf('Trail "%s" was not found.', $trailId));

            return Command::FAILURE;
        }

        $this->trailPointsImporter->importFile($trail, $file);

        if ($creatingTrail) {
            $io->success(sprintf(
                'Created trail "%s" (%s) and imported trail points from "%s".',
                $trail->getName(),
                $trail->getId(),
                $filePath,
            ));

            return Command::SUCCESS;
        }

        $io->success(sprintf(
            'Imported trail points from "%s" into trail "%s" (%s).',
            $filePath,
            $trail->getName(),
            $trail->getId() ?? $trailId,
        ));

        return Command::SUCCESS;
    }

    private function createTrailFromFile(mixed $file, string $filePath): Trail
    {
        $trailName = trim((string) ($file->trk->name ?? ''));

        if ($trailName === '') {
            $trailName = pathinfo($filePath, PATHINFO_FILENAME);
        }

        return new Trail(
            $trailName,
            self::DEFAULT_DIFFICULTY,
            self::DEFAULT_LENGTH,
        );
    }
}

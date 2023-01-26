<?php

declare(strict_types=1);

namespace App\Command\Docker;

use App\Service\DockerScanImagesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:docker-scan-images',
    description: 'Scans docker images and publishes.',
)]
class ScanImagesCommand extends Command
{
    private const OPTION_PATH = 'path';
    private const OPTION_BUILD = 'build';

    public function __construct(
        private readonly string $rootPath
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::OPTION_PATH, 'p', InputOption::VALUE_OPTIONAL, 'Path.', sprintf('%s/images', $this->rootPath))
            ->addOption(self::OPTION_BUILD, 'b', InputOption::VALUE_OPTIONAL, 'Only build option.', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getOption(self::OPTION_PATH);
        $onlyBuild = (bool) $input->getOption(self::OPTION_BUILD);
        $dockerScanService = new DockerScanImagesService([
            'path' => $path,
            'only-build' => $onlyBuild,
        ]);
        $dockerScanService->scan();

        return Command::SUCCESS;
    }
}

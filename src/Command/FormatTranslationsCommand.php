<?php

declare(strict_types=1);

namespace App\Command;

use App\Util\TranslationFormatter;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app:format-translations',
    description: 'Sorting and grouping translation yaml files.',
)]
class FormatTranslationsCommand extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this // @phpstan-ignore-line
            ->addArgument(
                'path',
                InputArgument::IS_ARRAY,
                'files or directories to format',
                [(string) $this->parameterBag->get('translator.default_path') ?: '']
            )
            ->addOption('indentation', null, InputOption::VALUE_REQUIRED, 'file indentation level', '2')
            ->addOption('sort', null, InputOption::VALUE_REQUIRED, 'do no sort file translations', 'yes')
            ->addOption('group', null, InputOption::VALUE_REQUIRED, 'do not group translations', 'yes')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'do not write output file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formatter = new TranslationFormatter(
            (int) $input->getOption('indentation'),
            !$input->getOption('dry-run'),
            filter_var($input->getOption('sort'), FILTER_VALIDATE_BOOLEAN),
            filter_var($input->getOption('group'), FILTER_VALIDATE_BOOLEAN)
        );
        $changed = false;
        $filesChecked = 0;
        foreach ($this->findFiles($input->getArgument('path')) as $fileInfo) {
            $fileChanged = $formatter->formatFile($fileInfo->getPathname());
            ++$filesChecked;
            if ($fileChanged && !$output->isQuiet()) {
                $output->writeln(sprintf('File <error>%s</error> fixed.', $fileInfo->getPathname()));
            } elseif ($output->isVerbose()) {
                $output->writeln(sprintf('File <comment>%s</comment> not changed.', $fileInfo->getPathname()));
            }
            $changed = $changed || $fileChanged;
        }

        if (!$output->isQuiet()) {
            $output->writeln(sprintf('Processed <info>%d</info> translation files.', $filesChecked));
        }

        return $changed ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * @param string[] $paths
     *
     * @return SplFileInfo[]
     */
    private function findFiles(array $paths): iterable
    {
        $directories = [];
        $files = [];
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $directories[] = $path;
            } else {
                $files[] = $path;
            }
        }

        return (new Finder())
            ->in($directories)
            ->name(['*.yml', '*.yaml'])
            ->append($files);
    }
}

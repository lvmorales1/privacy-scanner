<?php

declare(strict_types=1);

namespace PrivacyScanner\Commands;

use PrivacyScanner\Detectors\PersonalDataDetector;
use PrivacyScanner\Detectors\SecretDetector;
use PrivacyScanner\Reporters\ConsoleReporter;
use PrivacyScanner\Reporters\JsonReporter;
use PrivacyScanner\Reporters\ReporterInterface;
use PrivacyScanner\Scanner\FileScanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ScanCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('scan')
            ->setDescription('Scan a path for secrets and personal data')
            ->addArgument('path', InputArgument::REQUIRED, 'File or directory to scan')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format: console or json', 'console');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        $realPath = realpath($path);

        if ($realPath === false) {
            $output->writeln(sprintf('<error>Path not found: %s</error>', $path));
            return Command::FAILURE;
        }

        $scanner = new FileScanner([
            new SecretDetector(),
            new PersonalDataDetector(),
        ]);

        $result = $scanner->scan($realPath);

        $this->resolveReporter($input->getOption('format'))->report($result, $output);

        return $result->isEmpty() ? Command::SUCCESS : Command::FAILURE;
    }

    private function resolveReporter(string $format): ReporterInterface
    {
        return match ($format) {
            'json'  => new JsonReporter(),
            default => new ConsoleReporter(),
        };
    }
}

<?php

declare(strict_types=1);

namespace PrivacyScanner\Reporters;

use PrivacyScanner\Enums\Severity;
use PrivacyScanner\Finding;
use PrivacyScanner\Scanner\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleReporter implements ReporterInterface
{
    public function report(ScanResult $result, OutputInterface $output): void
    {
        if ($result->isEmpty()) {
            $output->writeln('<info>No issues found.</info>');
            $output->writeln('');
            $output->writeln(sprintf(
                'Scanned <comment>%d</comment> file(s) — Risk Score: <info>0/100</info>',
                $result->getFilesScanned()
            ));
            return;
        }

        foreach ($result->getFindings() as $finding) {
            $this->printFinding($finding, $output);
        }

        $score = $result->getRiskScore();
        $scoreColor = $score >= 70 ? 'red' : ($score >= 40 ? 'yellow' : 'green');

        $output->writeln(sprintf(
            'Scanned <comment>%d</comment> file(s) — <comment>%d</comment> finding(s) — Risk Score: <fg=%s>%d/100</>',
            $result->getFilesScanned(),
            count($result->getFindings()),
            $scoreColor,
            $score,
        ));
    }

    private function printFinding(Finding $finding, OutputInterface $output): void
    {
        $color = match ($finding->severity) {
            Severity::Low => 'green',
            Severity::Medium => 'yellow',
            Severity::High => 'red',
            Severity::Critical => 'magenta',
        };

        $output->writeln(sprintf('<fg=%s>[%s]</> %s', $color, $finding->severity->value, $finding->label));
        $output->writeln(sprintf('File:  %s', $finding->file));
        $output->writeln(sprintf('Line:  %d', $finding->line));
        $output->writeln(sprintf('Value: %s', $finding->masked));
        $output->writeln('');
    }
}

<?php

declare(strict_types=1);

namespace PrivacyScanner\Reporters;

use PrivacyScanner\Finding;
use PrivacyScanner\Scanner\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

final class JsonReporter implements ReporterInterface
{
    public function report(ScanResult $result, OutputInterface $output): void
    {
        $data = [
            'risk_score' => $result->getRiskScore(),
            'files_scanned' => $result->getFilesScanned(),
            'total_findings'=> count($result->getFindings()),
            'findings' => array_map($this->serializeFinding(...), $result->getFindings()),
        ];

        $output->writeln(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function serializeFinding(Finding $finding): array
    {
        return [
            'type' => $finding->type->value,
            'category' => $finding->category,
            'label' => $finding->label,
            'severity' => $finding->severity->value,
            'risk_score' => $finding->riskScore,
            'file' => $finding->file,
            'line' => $finding->line,
            'masked_value' => $finding->masked,
        ];
    }
}

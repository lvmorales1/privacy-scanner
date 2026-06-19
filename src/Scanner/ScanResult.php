<?php

declare(strict_types=1);

namespace PrivacyScanner\Scanner;

use PrivacyScanner\Finding;

final class ScanResult
{
    /** @var Finding[] */
    private array $findings = [];

    private int $filesScanned = 0;

    public function addFindings(array $findings): void
    {
        foreach ($findings as $finding) {
            $this->findings[] = $finding;
        }
    }

    public function incrementFilesScanned(): void
    {
        $this->filesScanned++;
    }

    /** @return Finding[] */
    public function getFindings(): array
    {
        return $this->findings;
    }

    public function getFilesScanned(): int
    {
        return $this->filesScanned;
    }

    public function isEmpty(): bool
    {
        return empty($this->findings);
    }

    public function getRiskScore(): int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        $total = array_sum(array_map(fn(Finding $f) => $f->riskScore, $this->findings));

        return min(100, $total);
    }
}

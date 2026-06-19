<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Rules;

use Lvmorales1\PrivacyScanner\Finding;

interface RuleInterface
{
    public function getName(): string;

    public function getCategory(): string;

    public function getRiskScore(): int;

    public function check(string $line, int $lineNumber, string $filePath): ?Finding;
}

<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Rules\PersonalData;

use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Rules\AbstractRule;

final class EmailRule extends AbstractRule
{
    public function getName(): string     { return 'Email Address'; }
    public function getCategory(): string { return 'email'; }
    public function getRiskScore(): int   { return 3; }
    protected function getType(): FindingType { return FindingType::PersonalData; }

    protected function getPattern(): string
    {
        return '/\b[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}\b/';
    }
}

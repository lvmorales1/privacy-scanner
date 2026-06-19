<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Rules\Secrets;

use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Rules\AbstractRule;

final class DatabaseUrlRule extends AbstractRule
{
    public function getName(): string     { return 'Database Connection URL'; }
    public function getCategory(): string { return 'database_url'; }
    public function getRiskScore(): int   { return 8; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/(mysql|postgresql|postgres|mongodb|redis):\/\/[^:\s]+:[^@\s]+@[^\s"\']+/i';
    }
}

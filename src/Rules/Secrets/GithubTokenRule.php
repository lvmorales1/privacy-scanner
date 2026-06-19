<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Rules\Secrets;

use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Rules\AbstractRule;

final class GithubTokenRule extends AbstractRule
{
    public function getName(): string     { return 'GitHub Token'; }
    public function getCategory(): string { return 'github_token'; }
    public function getRiskScore(): int   { return 8; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/gh[pousr]_[A-Za-z0-9]{36}/';
    }
}

<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Rules\Secrets;

use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Rules\AbstractRule;

final class OpenAiKeyRule extends AbstractRule
{
    public function getName(): string     { return 'OpenAI API Key'; }
    public function getCategory(): string { return 'openai_key'; }
    public function getRiskScore(): int   { return 8; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/sk-(proj-)?[A-Za-z0-9\-_]{40,}/';
    }
}

<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Rules\Secrets;

use Lvmorales1\PrivacyScanner\Enums\FindingType;
use Lvmorales1\PrivacyScanner\Rules\AbstractRule;

final class JwtRule extends AbstractRule
{
    public function getName(): string     { return 'JSON Web Token'; }
    public function getCategory(): string { return 'jwt'; }
    public function getRiskScore(): int   { return 7; }
    protected function getType(): FindingType { return FindingType::Secret; }

    protected function getPattern(): string
    {
        return '/eyJ[A-Za-z0-9\-_]+\.eyJ[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+/';
    }
}

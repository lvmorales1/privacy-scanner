<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Detectors;

use Lvmorales1\PrivacyScanner\Rules\RuleInterface;
use Lvmorales1\PrivacyScanner\Rules\Secrets\AwsKeyRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\DatabaseUrlRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\GithubTokenRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\JwtRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\OpenAiKeyRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\SshPrivateKeyRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\StripeKeyRule;

final class SecretDetector implements DetectorInterface
{
    /** @var RuleInterface[] */
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new SshPrivateKeyRule(),
            new AwsKeyRule(),
            new StripeKeyRule(),
            new GithubTokenRule(),
            new OpenAiKeyRule(),
            new DatabaseUrlRule(),
            new JwtRule(),
        ];
    }

    public function detect(string $filePath, string $content): array
    {
        $findings = [];
        $lines = explode("\n", $content);

        foreach ($lines as $index => $line) {
            foreach ($this->rules as $rule) {
                $finding = $rule->check($line, $index + 1, $filePath);

                if ($finding !== null) {
                    $findings[] = $finding;
                }
            }
        }

        return $findings;
    }
}

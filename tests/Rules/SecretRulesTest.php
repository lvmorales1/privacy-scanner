<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Tests\Rules;

use Lvmorales1\PrivacyScanner\Enums\Severity;
use Lvmorales1\PrivacyScanner\Rules\Secrets\AwsKeyRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\DatabaseUrlRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\GithubTokenRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\JwtRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\OpenAiKeyRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\SshPrivateKeyRule;
use Lvmorales1\PrivacyScanner\Rules\Secrets\StripeKeyRule;
use PHPUnit\Framework\TestCase;

final class SecretRulesTest extends TestCase
{
    public function test_aws_key_is_detected(): void
    {
        $rule = new AwsKeyRule();
        $finding = $rule->check('$key = "AKIAIOSFODNN7EXAMPLE";', 1, 'config.php');

        $this->assertNotNull($finding);
        $this->assertSame('aws_key', $finding->category);
        $this->assertSame(Severity::Critical, $finding->severity);
    }

    public function test_aws_key_is_not_triggered_on_random_text(): void
    {
        $rule = new AwsKeyRule();
        $finding = $rule->check('this is just a normal string without any key', 1, 'config.php');

        $this->assertNull($finding);
    }

    public function test_ssh_private_key_is_detected(): void
    {
        $rule = new SshPrivateKeyRule();
        $finding = $rule->check('-----BEGIN RSA PRIVATE KEY-----', 1, 'id_rsa');

        $this->assertNotNull($finding);
        $this->assertSame('ssh_private_key', $finding->category);
        $this->assertSame(Severity::Critical, $finding->severity);
    }

    public function test_ssh_key_detection_covers_all_key_types(): void
    {
        $rule = new SshPrivateKeyRule();

        foreach (['RSA', 'EC', 'DSA', 'OPENSSH'] as $type) {
            $finding = $rule->check("-----BEGIN {$type} PRIVATE KEY-----", 1, 'key_file');
            $this->assertNotNull($finding, "Expected {$type} private key to be detected");
        }
    }

    public function test_github_token_is_detected(): void
    {
        $rule = new GithubTokenRule();
        $finding = $rule->check('token = "ghp_16C7e42F292c6912E7710c838347Ae178B4a"', 1, '.env');

        $this->assertNotNull($finding);
        $this->assertSame('github_token', $finding->category);
    }

    public function test_database_url_is_detected(): void
    {
        $rule = new DatabaseUrlRule();
        $finding = $rule->check('DATABASE_URL=mysql://admin:password123@db.prod.example.com/myapp', 1, '.env');

        $this->assertNotNull($finding);
        $this->assertSame('database_url', $finding->category);
    }

    public function test_database_url_requires_credentials(): void
    {
        $rule = new DatabaseUrlRule();
        // URL without credentials should not match
        $finding = $rule->check('DATABASE_URL=mysql://db.prod.example.com/myapp', 1, '.env');

        $this->assertNull($finding);
    }

    public function test_jwt_is_detected(): void
    {
        $rule = new JwtRule();
        $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';
        $finding = $rule->check("Authorization: Bearer {$jwt}", 1, 'request.log');

        $this->assertNotNull($finding);
        $this->assertSame('jwt', $finding->category);
    }

    public function test_openai_key_is_detected(): void
    {
        $rule = new OpenAiKeyRule();
        $finding = $rule->check('OPENAI_API_KEY=sk-proj-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOP', 1, '.env');

        $this->assertNotNull($finding);
        $this->assertSame('openai_key', $finding->category);
    }

    public function test_stripe_live_key_is_detected(): void
    {
        $rule = new StripeKeyRule();
        $finding = $rule->check('STRIPE_KEY=sk_live_abcdefghijklmnopqrstuvwx', 1, '.env');

        $this->assertNotNull($finding);
        $this->assertSame('stripe_key', $finding->category);
    }

    public function test_stripe_test_key_is_not_detected(): void
    {
        $rule = new StripeKeyRule();
        $finding = $rule->check('STRIPE_KEY=sk_test_abcdefghijklmnopqrstuvwx', 1, '.env');

        $this->assertNull($finding);
    }

    public function test_finding_value_is_masked(): void
    {
        $rule = new AwsKeyRule();
        $finding = $rule->check('AKIAIOSFODNN7EXAMPLE', 1, 'file.txt');

        $this->assertNotNull($finding);
        $this->assertStringNotContainsString('AKIAIOSFODNN7EXAMPLE', $finding->masked);
        $this->assertStringContainsString('*', $finding->masked);
    }

    public function test_finding_reports_correct_line_number(): void
    {
        $rule = new AwsKeyRule();
        $finding = $rule->check('AKIAIOSFODNN7EXAMPLE', 42, 'file.txt');

        $this->assertNotNull($finding);
        $this->assertSame(42, $finding->line);
    }
}

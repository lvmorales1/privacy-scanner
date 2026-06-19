<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Tests\Rules;

use Lvmorales1\PrivacyScanner\Rules\PersonalData\BrazilianPhoneRule;
use Lvmorales1\PrivacyScanner\Rules\PersonalData\CnpjRule;
use Lvmorales1\PrivacyScanner\Rules\PersonalData\CpfRule;
use Lvmorales1\PrivacyScanner\Rules\PersonalData\EmailRule;
use PHPUnit\Framework\TestCase;

final class PersonalDataRulesTest extends TestCase
{
    public function test_formatted_cpf_is_detected(): void
    {
        $rule = new CpfRule();
        $finding = $rule->check('cpf: 123.456.789-09', 1, 'users.csv');

        $this->assertNotNull($finding);
        $this->assertSame('cpf', $finding->category);
    }

    public function test_unformatted_cpf_is_not_detected(): void
    {
        // We only match formatted CPFs to avoid false positives on random numbers
        $rule = new CpfRule();
        $finding = $rule->check('12345678909', 1, 'users.csv');

        $this->assertNull($finding);
    }

    public function test_cnpj_is_detected(): void
    {
        $rule = new CnpjRule();
        $finding = $rule->check('cnpj: 11.222.333/0001-81', 1, 'companies.csv');

        $this->assertNotNull($finding);
        $this->assertSame('cnpj', $finding->category);
    }

    public function test_email_is_detected(): void
    {
        $rule = new EmailRule();
        $finding = $rule->check('contact: user@example.com', 1, 'contacts.txt');

        $this->assertNotNull($finding);
        $this->assertSame('email', $finding->category);
    }

    public function test_email_with_subdomain_is_detected(): void
    {
        $rule = new EmailRule();
        $finding = $rule->check('user@mail.company.com.br', 1, 'file.txt');

        $this->assertNotNull($finding);
    }

    public function test_invalid_string_is_not_detected_as_email(): void
    {
        $rule = new EmailRule();
        $finding = $rule->check('this is just text with an @ sign', 1, 'file.txt');

        $this->assertNull($finding);
    }

    public function test_brazilian_phone_with_area_code_is_detected(): void
    {
        $rule = new BrazilianPhoneRule();
        $finding = $rule->check('phone: (11) 99999-8888', 1, 'contacts.txt');

        $this->assertNotNull($finding);
        $this->assertSame('phone_br', $finding->category);
    }

    public function test_brazilian_phone_with_country_code_is_detected(): void
    {
        $rule = new BrazilianPhoneRule();
        $finding = $rule->check('+55 11 98765-4321', 1, 'contacts.txt');

        $this->assertNotNull($finding);
    }
}

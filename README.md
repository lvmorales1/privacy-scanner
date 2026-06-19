# Privacy Scanner

Scans files and directories for secrets and personal data that shouldn't be in your codebase — API keys, database passwords, CPFs, emails, and more.

## Install

```bash
composer install
```

## Usage

```bash
php bin/privacy-scan scan .
php bin/privacy-scan scan config/services.php
php bin/privacy-scan scan . --format=json
```

## Example output

```
[CRITICAL] AWS Access Key
File:  config/services.php
Line:  12
Value: AKIA************MPLE

[HIGH] CPF (Brazilian Tax ID)
File:  storage/customers.csv
Line:  45
Value: 123.******9-09

Scanned 42 file(s) — 2 finding(s) — Risk Score: 15/100
```

Exit code is `0` when clean, `1` when findings are found.

## Adding a new rule

Create a class in `src/Rules/Secrets/` or `src/Rules/PersonalData/`, extend `AbstractRule`, and register it in the corresponding detector.

```php
final class MyTokenRule extends AbstractRule
{
    public function getName(): string          { return 'My Token'; }
    public function getCategory(): string      { return 'my_token'; }
    public function getRiskScore(): int        { return 8; }
    protected function getType(): FindingType  { return FindingType::Secret; }
    protected function getPattern(): string    { return '/mytoken_[A-Za-z0-9]{32}/'; }
}
```

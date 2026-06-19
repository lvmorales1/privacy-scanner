<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Reporters;

use Lvmorales1\PrivacyScanner\Scanner\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

interface ReporterInterface
{
    public function report(ScanResult $result, OutputInterface $output): void;
}

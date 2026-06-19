<?php

declare(strict_types=1);

namespace PrivacyScanner\Reporters;

use PrivacyScanner\Scanner\ScanResult;
use Symfony\Component\Console\Output\OutputInterface;

interface ReporterInterface
{
    public function report(ScanResult $result, OutputInterface $output): void;
}

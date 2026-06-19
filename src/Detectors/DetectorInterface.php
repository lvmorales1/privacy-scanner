<?php

declare(strict_types=1);

namespace Lvmorales1\PrivacyScanner\Detectors;

use Lvmorales1\PrivacyScanner\Finding;

interface DetectorInterface
{
    /** @return Finding[] */
    public function detect(string $filePath, string $content): array;
}

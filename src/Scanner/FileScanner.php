<?php

declare(strict_types=1);

namespace PrivacyScanner\Scanner;

use PrivacyScanner\Detectors\DetectorInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FileScanner
{
    private const EXCLUDED_DIRS = ['vendor', 'node_modules', '.git'];

    private const EXCLUDED_EXTENSIONS = ['lock', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf', 'zip', 'gz', 'ico', 'woff', 'woff2', 'ttf'];

    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

    /** @param DetectorInterface[] $detectors */
    public function __construct(private readonly array $detectors) {}

    public function scan(string $path): ScanResult
    {
        $result = new ScanResult();

        if (is_file($path)) {
            $this->processFile($path, basename($path), $result);
            return $result;
        }

        $finder = (new Finder())
            ->files()
            ->in($path)
            ->exclude(self::EXCLUDED_DIRS);

        foreach ($finder as $file) {
            if ($this->shouldSkip($file)) {
                continue;
            }

            $this->processFile($file->getRealPath(), $file->getRelativePathname(), $result);
        }

        return $result;
    }

    private function processFile(string $realPath, string $displayPath, ScanResult $result): void
    {
        $content = file_get_contents($realPath);

        if ($content === false) {
            return;
        }

        $result->incrementFilesScanned();

        foreach ($this->detectors as $detector) {
            $result->addFindings($detector->detect($displayPath, $content));
        }
    }

    private function shouldSkip(SplFileInfo $file): bool
    {
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return true;
        }

        return in_array($file->getExtension(), self::EXCLUDED_EXTENSIONS, strict: true);
    }
}

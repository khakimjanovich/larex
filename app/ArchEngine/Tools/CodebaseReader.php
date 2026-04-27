<?php

namespace App\ArchEngine\Tools;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class CodebaseReader
{
    private const IgnoredDirectories = ['vendor', '.git', 'node_modules', 'storage'];

    /**
     * @param  list<string>  $extensions  e.g. ['php', 'json']
     * @return list<string>
     */
    public function listFiles(string $path, array $extensions = []): array
    {
        if (! is_dir($path)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if (! $item->isFile()) {
                continue;
            }

            if ($this->isInIgnoredDirectory($item->getPathname(), $path)) {
                continue;
            }

            if ($extensions !== [] && ! in_array($item->getExtension(), $extensions, true)) {
                continue;
            }

            $files[] = $item->getPathname();
        }

        sort($files);

        return $files;
    }

    public function readFile(string $path): string
    {
        if (! is_file($path)) {
            return '';
        }

        return (string) file_get_contents($path);
    }

    /**
     * @return list<array{file: string, line: int, match: string}>
     */
    public function searchPattern(string $needle, string $path): array
    {
        $results = [];

        foreach ($this->listFiles($path) as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES) ?: [];

            foreach ($lines as $index => $line) {
                if (str_contains($line, $needle)) {
                    $results[] = [
                        'file' => $file,
                        'line' => $index + 1,
                        'match' => trim($line),
                    ];
                }
            }
        }

        return $results;
    }

    private function isInIgnoredDirectory(string $filePath, string $basePath): bool
    {
        $relative = ltrim(substr($filePath, strlen(rtrim($basePath, '/'))), '/');
        $parts = explode('/', $relative);

        foreach ($parts as $part) {
            if (in_array($part, self::IgnoredDirectories, true)) {
                return true;
            }
        }

        return false;
    }
}

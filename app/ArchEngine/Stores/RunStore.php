<?php

namespace App\ArchEngine\Stores;

final class RunStore
{
    public function __construct(
        private readonly string $baseDir,
    ) {}

    public static function default(): self
    {
        return new self(base_path('.larex/runs'));
    }

    public function createRun(): string
    {
        $existing = $this->existingRunNumbers();
        $next = $existing === [] ? 1 : max($existing) + 1;
        $runId = 'RUN-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);

        $dir = $this->runDir($runId);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $runId;
    }

    public function saveArtifact(string $runId, string $stage, array $data): void
    {
        $dir = $this->runDir($runId);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents(
            $dir.'/'.$stage.'.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        );
    }

    public function loadArtifact(string $runId, string $stage): ?array
    {
        $path = $this->runDir($runId).'/'.$stage.'.json';

        if (! is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false || trim($contents) === '') {
            return null;
        }

        return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    }

    public function runExists(string $runId): bool
    {
        return is_dir($this->runDir($runId));
    }

    private function runDir(string $runId): string
    {
        return rtrim($this->baseDir, '/').'/'.$runId;
    }

    /** @return list<int> */
    private function existingRunNumbers(): array
    {
        if (! is_dir($this->baseDir)) {
            return [];
        }

        $numbers = [];

        foreach (scandir($this->baseDir) ?: [] as $entry) {
            if (preg_match('/^RUN-(\d+)$/', $entry, $m)) {
                $numbers[] = (int) $m[1];
            }
        }

        return $numbers;
    }
}

<?php

namespace App\ArchEngine\TargetProject;

use App\ArchEngine\DTO\TargetProjectFacts;
use JsonException;

final class TargetProjectDetector
{
    private const SchemaVersion = 'target-project-facts-v1';

    /**
     * @var array<string, string>
     */
    private const DevToolPackages = [
        'pestphp/pest' => 'pest',
        'phpunit/phpunit' => 'phpunit',
        'laravel/pint' => 'pint',
        'phpstan/phpstan' => 'phpstan',
        'larastan/larastan' => 'larastan',
        'rector/rector' => 'rector',
    ];

    public function detect(string $path): TargetProjectFacts
    {
        $projectPath = $this->normalizePath($path);
        $composerPath = $projectPath.'/composer.json';

        if (! is_file($composerPath)) {
            return $this->unknown($projectPath, ['target.missing_composer_json']);
        }

        try {
            $composer = $this->readComposerJson($composerPath);
        } catch (JsonException) {
            return $this->unknown($projectPath, ['target.invalid_composer_json']);
        }

        $productionPackages = $this->packageMap($composer, 'require');
        $devPackages = $this->packageMap($composer, 'require-dev');
        $allPackages = $this->packageNames($productionPackages, $devPackages);
        $evidenceRefs = ['target.composer_json'];

        $hasArtisan = is_file($projectPath.'/artisan');
        $hasBootstrapApp = is_file($projectPath.'/bootstrap/app.php');

        if ($hasArtisan) {
            $evidenceRefs[] = 'target.artisan';
        }

        if ($hasBootstrapApp) {
            $evidenceRefs[] = 'target.bootstrap_app';
        }

        if ($this->hasLaravelExtra($composer)) {
            $evidenceRefs[] = 'target.composer_extra_laravel';
        }

        $targetProject = new TargetProject(
            path: $projectPath,
            type: $this->detectProjectType($composer, $productionPackages, $devPackages, $hasArtisan, $hasBootstrapApp),
        );

        return new TargetProjectFacts(
            schemaVersion: self::SchemaVersion,
            path: $targetProject->path,
            projectType: $targetProject->type,
            composerName: $this->stringValue($composer['name'] ?? null),
            phpConstraint: $this->stringValue($productionPackages['php'] ?? null),
            laravelVersion: $this->detectLaravelVersion($productionPackages, $devPackages),
            installedPackages: $allPackages,
            devTools: $this->detectDevTools($devPackages),
            testRunner: $this->detectTestRunner($devPackages),
            evidenceRefs: array_values(array_unique($evidenceRefs)),
        );
    }

    /**
     * @param  list<string>  $evidenceRefs
     */
    private function unknown(string $path, array $evidenceRefs): TargetProjectFacts
    {
        return new TargetProjectFacts(
            schemaVersion: self::SchemaVersion,
            path: $this->normalizePath($path),
            projectType: TargetProject::Unknown,
            composerName: null,
            phpConstraint: null,
            laravelVersion: null,
            installedPackages: [],
            devTools: [],
            testRunner: 'unknown',
            evidenceRefs: $evidenceRefs,
        );
    }

    private function normalizePath(string $path): string
    {
        return realpath($path) ?: $path;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function readComposerJson(string $composerPath): array
    {
        $contents = file_get_contents($composerPath);

        if ($contents === false) {
            return [];
        }

        $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array<string, mixed>  $composer
     * @return array<string, string>
     */
    private function packageMap(array $composer, string $section): array
    {
        $packages = $composer[$section] ?? [];

        if (! is_array($packages)) {
            return [];
        }

        $packageMap = [];

        foreach ($packages as $name => $constraint) {
            if (is_string($name) && is_string($constraint)) {
                $packageMap[$name] = $constraint;
            }
        }

        return $packageMap;
    }

    /**
     * @param  array<string, string>  $productionPackages
     * @param  array<string, string>  $devPackages
     * @return list<string>
     */
    private function packageNames(array $productionPackages, array $devPackages): array
    {
        $packageNames = array_values(array_unique(array_merge(
            array_keys($productionPackages),
            array_keys($devPackages),
        )));

        sort($packageNames);

        return $packageNames;
    }

    /**
     * @param  array<string, mixed>  $composer
     * @param  array<string, string>  $productionPackages
     * @param  array<string, string>  $devPackages
     */
    private function detectProjectType(
        array $composer,
        array $productionPackages,
        array $devPackages,
        bool $hasArtisan,
        bool $hasBootstrapApp,
    ): string {
        if ($hasArtisan && ($hasBootstrapApp || array_key_exists('laravel/framework', $productionPackages))) {
            return TargetProject::LaravelApp;
        }

        if ($this->isLaravelPackage($composer, $productionPackages, $devPackages)) {
            return TargetProject::LaravelPackage;
        }

        return TargetProject::PhpPackage;
    }

    /**
     * @param  array<string, mixed>  $composer
     * @param  array<string, string>  $productionPackages
     * @param  array<string, string>  $devPackages
     */
    private function isLaravelPackage(array $composer, array $productionPackages, array $devPackages): bool
    {
        if ($this->hasLaravelExtra($composer)) {
            return true;
        }

        return array_key_exists('laravel/framework', $productionPackages)
            || array_key_exists('illuminate/support', $productionPackages)
            || array_key_exists('orchestra/testbench', $devPackages);
    }

    /**
     * @param  array<string, mixed>  $composer
     */
    private function hasLaravelExtra(array $composer): bool
    {
        return isset($composer['extra'])
            && is_array($composer['extra'])
            && isset($composer['extra']['laravel'])
            && is_array($composer['extra']['laravel']);
    }

    /**
     * @param  array<string, string>  $productionPackages
     * @param  array<string, string>  $devPackages
     */
    private function detectLaravelVersion(array $productionPackages, array $devPackages): ?string
    {
        return $productionPackages['laravel/framework']
            ?? $productionPackages['illuminate/support']
            ?? $devPackages['orchestra/testbench']
            ?? null;
    }

    /**
     * @param  array<string, string>  $devPackages
     * @return list<string>
     */
    private function detectDevTools(array $devPackages): array
    {
        $devTools = [];

        foreach (self::DevToolPackages as $packageName => $toolName) {
            if (array_key_exists($packageName, $devPackages)) {
                $devTools[] = $toolName;
            }
        }

        return $devTools;
    }

    /**
     * @param  array<string, string>  $devPackages
     */
    private function detectTestRunner(array $devPackages): string
    {
        if (array_key_exists('pestphp/pest', $devPackages)) {
            return 'pest';
        }

        if (array_key_exists('phpunit/phpunit', $devPackages)) {
            return 'phpunit';
        }

        return 'unknown';
    }

    private function stringValue(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}

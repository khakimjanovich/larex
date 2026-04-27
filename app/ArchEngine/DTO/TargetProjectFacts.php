<?php

namespace App\ArchEngine\DTO;

final readonly class TargetProjectFacts
{
    /**
     * @param  list<string>  $installedPackages
     * @param  list<string>  $devTools
     * @param  list<string>  $evidenceRefs
     */
    public function __construct(
        public string $schemaVersion,
        public string $path,
        public string $projectType,
        public ?string $composerName,
        public ?string $phpConstraint,
        public ?string $laravelVersion,
        public array $installedPackages,
        public array $devTools,
        public string $testRunner,
        public array $evidenceRefs,
    ) {}

    /**
     * @return array{
     *     schema_version: string,
     *     path: string,
     *     project_type: string,
     *     composer_name: string|null,
     *     php_constraint: string|null,
     *     laravel_version: string|null,
     *     installed_packages: list<string>,
     *     dev_tools: list<string>,
     *     test_runner: string,
     *     evidence_refs: list<string>
     * }
     */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'path' => $this->path,
            'project_type' => $this->projectType,
            'composer_name' => $this->composerName,
            'php_constraint' => $this->phpConstraint,
            'laravel_version' => $this->laravelVersion,
            'installed_packages' => $this->installedPackages,
            'dev_tools' => $this->devTools,
            'test_runner' => $this->testRunner,
            'evidence_refs' => $this->evidenceRefs,
        ];
    }
}

<?php

namespace App\ArchEngine\DTO;

final readonly class PatchPlan
{
    /**
     * @param  list<Mutation>  $mutations
     */
    public function __construct(
        public string $schemaVersion,
        public string $runId,
        public array $mutations,
    ) {}

    /**
     * @return array{schema_version: string, run_id: string, mutations: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'run_id' => $this->runId,
            'mutations' => array_map(fn (Mutation $m) => $m->toArray(), $this->mutations),
        ];
    }

    /**
     * @param  array{schema_version?: string, run_id?: string, mutations?: list<array<string, mixed>>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schemaVersion: (string) ($data['schema_version'] ?? 'patch-plan-v1'),
            runId: (string) ($data['run_id'] ?? ''),
            mutations: array_map(
                fn (array $m) => Mutation::fromArray($m),
                $data['mutations'] ?? [],
            ),
        );
    }
}

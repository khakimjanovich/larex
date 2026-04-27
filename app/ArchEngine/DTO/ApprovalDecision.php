<?php

namespace App\ArchEngine\DTO;

final readonly class ApprovalDecision
{
    public function __construct(
        public string $schemaVersion,
        public string $runId,
        public string $approvedAt,
        public string $approvedBy,
    ) {}

    /**
     * @return array{schema_version: string, run_id: string, approved_at: string, approved_by: string}
     */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'run_id' => $this->runId,
            'approved_at' => $this->approvedAt,
            'approved_by' => $this->approvedBy,
        ];
    }

    /**
     * @param  array{schema_version?: string, run_id?: string, approved_at?: string, approved_by?: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schemaVersion: (string) ($data['schema_version'] ?? 'approval-decision-v1'),
            runId: (string) ($data['run_id'] ?? ''),
            approvedAt: (string) ($data['approved_at'] ?? ''),
            approvedBy: (string) ($data['approved_by'] ?? 'human'),
        );
    }
}

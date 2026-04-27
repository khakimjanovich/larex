<?php

namespace App\ArchEngine\DTO;

final readonly class ProposedChange
{
    /**
     * @param  list<string>  $evidenceRefs
     */
    public function __construct(
        public string $type,
        public string $path,
        public string $description,
        public string $rationale,
        public array $evidenceRefs,
    ) {}

    /**
     * @return array{type: string, path: string, description: string, rationale: string, evidence_refs: list<string>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'path' => $this->path,
            'description' => $this->description,
            'rationale' => $this->rationale,
            'evidence_refs' => $this->evidenceRefs,
        ];
    }

    /**
     * @param  array{type: string, path: string, description: string, rationale: string, evidence_refs: list<string>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: (string) ($data['type'] ?? ''),
            path: (string) ($data['path'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            rationale: (string) ($data['rationale'] ?? ''),
            evidenceRefs: array_values(array_map('strval', $data['evidence_refs'] ?? [])),
        );
    }
}

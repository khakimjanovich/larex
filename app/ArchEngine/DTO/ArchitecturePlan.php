<?php

namespace App\ArchEngine\DTO;

final readonly class ArchitecturePlan
{
    /**
     * @param  list<ProposedChange>  $proposedChanges
     * @param  list<string>  $risks
     * @param  list<string>  $openQuestions
     */
    public function __construct(
        public string $schemaVersion,
        public string $title,
        public string $summary,
        public array $proposedChanges,
        public array $risks,
        public array $openQuestions,
    ) {}

    /**
     * @return array{
     *     schema_version: string,
     *     title: string,
     *     summary: string,
     *     proposed_changes: list<array{type: string, path: string, description: string, rationale: string, evidence_refs: list<string>}>,
     *     risks: list<string>,
     *     open_questions: list<string>
     * }
     */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'title' => $this->title,
            'summary' => $this->summary,
            'proposed_changes' => array_map(fn (ProposedChange $c) => $c->toArray(), $this->proposedChanges),
            'risks' => $this->risks,
            'open_questions' => $this->openQuestions,
        ];
    }
}

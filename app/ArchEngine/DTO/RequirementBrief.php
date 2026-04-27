<?php

namespace App\ArchEngine\DTO;

final readonly class RequirementBrief
{
    /**
     * @param  list<string>  $inScope
     * @param  list<string>  $outOfScope
     * @param  list<string>  $acceptanceCriteria
     * @param  list<string>  $constraints
     * @param  list<string>  $openQuestions
     */
    public function __construct(
        public string $schemaVersion,
        public string $title,
        public string $goal,
        public ?string $targetProjectPath,
        public array $inScope,
        public array $outOfScope,
        public array $acceptanceCriteria,
        public array $constraints,
        public array $openQuestions,
    ) {}

    /**
     * @return array{
     *     schema_version: string,
     *     title: string,
     *     goal: string,
     *     target_project_path: string|null,
     *     in_scope: list<string>,
     *     out_of_scope: list<string>,
     *     acceptance_criteria: list<string>,
     *     constraints: list<string>,
     *     open_questions: list<string>
     * }
     */
    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'title' => $this->title,
            'goal' => $this->goal,
            'target_project_path' => $this->targetProjectPath,
            'in_scope' => $this->inScope,
            'out_of_scope' => $this->outOfScope,
            'acceptance_criteria' => $this->acceptanceCriteria,
            'constraints' => $this->constraints,
            'open_questions' => $this->openQuestions,
        ];
    }
}

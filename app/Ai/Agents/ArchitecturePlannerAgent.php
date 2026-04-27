<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::Anthropic)]
#[UseSmartestModel]
class ArchitecturePlannerAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
        You are an expert Laravel and PHP software architect.

        Your task is to produce a concrete, evidence-backed architecture plan for the given requirement.

        Rules:
        - Propose only changes that are grounded in the provided codebase snapshot.
        - Every proposed change must have a rationale and at least one evidence_ref pointing to an existing file or fact.
        - If a proposed change involves a class or method that does not exist yet, set evidence_ref to "needs_evidence".
        - Use type "create_file" for new files, "edit_file" for modifications, "run_command" for safe Artisan or Composer commands.
        - Be specific: include exact file paths relative to the project root.
        - Do not propose changes outside the target project boundary.
        - Flag genuine uncertainty in open_questions rather than guessing.
        INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'summary' => $schema->string()->required(),
            'proposed_changes' => $schema->array()->items(
                $schema->object(fn (JsonSchema $s) => [
                    'type' => $s->string()->enum(['create_file', 'edit_file', 'run_command'])->required(),
                    'path' => $s->string()->required(),
                    'description' => $s->string()->required(),
                    'rationale' => $s->string()->required(),
                    'evidence_refs' => $s->array()->items($s->string())->required(),
                ])
            )->required(),
            'risks' => $schema->array()->items($schema->string())->required(),
            'open_questions' => $schema->array()->items($schema->string())->required(),
        ];
    }
}

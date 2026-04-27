<?php

namespace App\ArchEngine\Stages;

use App\Ai\Agents\ArchitecturePlannerAgent;
use App\ArchEngine\DTO\ArchitecturePlan;
use App\ArchEngine\DTO\ProposedChange;
use App\ArchEngine\DTO\RequirementBrief;
use App\ArchEngine\DTO\TargetProjectFacts;
use App\ArchEngine\Pipeline\StageResult;
use App\ArchEngine\Tools\CodebaseReader;

final class ProduceArchitecturePlanStage
{
    private const SchemaVersion = 'architecture-plan-v1';

    private const MaxFileContentBytes = 80_000;

    public function __construct(
        private readonly RequirementBrief $requirement,
        private readonly TargetProjectFacts $facts,
        private readonly CodebaseReader $reader,
        private readonly ArchitecturePlannerAgent $agent,
    ) {}

    public function handle(): StageResult
    {
        if ($this->requirement->targetProjectPath === null) {
            return StageResult::blocked(
                evidenceRefs: ['requirement.missing_target_path'],
                errors: ['RequirementBrief has no targetProjectPath. Cannot read codebase.'],
            );
        }

        $prompt = $this->buildPrompt();

        $response = $this->agent->prompt($prompt);

        $plan = new ArchitecturePlan(
            schemaVersion: self::SchemaVersion,
            title: (string) ($response['title'] ?? ''),
            summary: (string) ($response['summary'] ?? ''),
            proposedChanges: array_map(
                fn (array $c) => ProposedChange::fromArray($c),
                $response['proposed_changes'] ?? [],
            ),
            risks: array_values(array_map('strval', $response['risks'] ?? [])),
            openQuestions: array_values(array_map('strval', $response['open_questions'] ?? [])),
        );

        return StageResult::succeeded(
            payload: $plan->toArray(),
            evidenceRefs: ['codebase.snapshot', 'requirement.brief'],
        );
    }

    private function buildPrompt(): string
    {
        $targetPath = (string) $this->requirement->targetProjectPath;

        $fileList = $this->reader->listFiles($targetPath, ['php']);
        $fileListing = implode("\n", array_map(
            fn (string $f) => '  '.ltrim(str_replace($targetPath, '', $f), '/'),
            $fileList,
        ));

        $codebaseSnapshot = $this->buildCodebaseSnapshot($targetPath);

        $inScope = implode("\n", array_map(fn ($s) => '- '.$s, $this->requirement->inScope));
        $acceptance = implode("\n", array_map(fn ($s) => '- '.$s, $this->requirement->acceptanceCriteria));
        $constraints = implode("\n", array_map(fn ($s) => '- '.$s, $this->requirement->constraints));
        $packages = implode(', ', $this->facts->installedPackages);

        return <<<PROMPT
        ## Requirement Brief

        Title: {$this->requirement->title}
        Goal: {$this->requirement->goal}

        In Scope:
        {$inScope}

        Acceptance Criteria:
        {$acceptance}

        Constraints:
        {$constraints}

        ## Target Project Facts

        Type: {$this->facts->projectType}
        PHP Constraint: {$this->facts->phpConstraint}
        Laravel Version: {$this->facts->laravelVersion}
        Packages: {$packages}

        ## PHP File Listing

        {$fileListing}

        ## Relevant Source Files

        {$codebaseSnapshot}

        Produce a concrete architecture plan for this requirement.
        PROMPT;
    }

    private function buildCodebaseSnapshot(string $targetPath): string
    {
        $archPath = $targetPath.'/app/ArchEngine';
        $files = $this->reader->listFiles(is_dir($archPath) ? $archPath : $targetPath, ['php']);

        $snapshot = '';
        $totalBytes = 0;

        foreach ($files as $file) {
            if ($totalBytes >= self::MaxFileContentBytes) {
                break;
            }

            $contents = $this->reader->readFile($file);
            $relativePath = ltrim(str_replace($targetPath, '', $file), '/');

            $chunk = "### {$relativePath}\n```php\n{$contents}\n```\n\n";
            $totalBytes += strlen($chunk);
            $snapshot .= $chunk;
        }

        return $snapshot;
    }
}

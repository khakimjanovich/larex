<?php

namespace App\ArchEngine\Stages;

use App\ArchEngine\DTO\RequirementBrief;
use App\ArchEngine\Pipeline\StageResult;
use App\ArchEngine\Tools\RequirementFileReader;

final class NormalizeRequirementStage
{
    private const SchemaVersion = 'requirement-brief-v1';

    public function __construct(
        private readonly string $requirementPath,
        private readonly RequirementFileReader $reader = new RequirementFileReader,
    ) {}

    public function handle(): StageResult
    {
        if (! $this->reader->exists($this->requirementPath)) {
            return StageResult::blocked(
                evidenceRefs: ['requirement.missing_file'],
                errors: ['Requirement file does not exist.'],
            );
        }

        $contents = $this->reader->read($this->requirementPath);

        if ($contents === null || trim($contents) === '') {
            return StageResult::blocked(
                evidenceRefs: ['requirement.empty_file'],
                errors: ['Requirement file is empty.'],
            );
        }

        $sections = $this->parseSections($contents);
        $errors = $this->validationErrors($sections);

        if ($errors !== []) {
            return StageResult::blocked(
                evidenceRefs: ['requirement.file'],
                errors: $errors,
            );
        }

        $brief = new RequirementBrief(
            schemaVersion: self::SchemaVersion,
            title: $this->parseTitle($contents),
            goal: $this->paragraph($sections['goal'] ?? ''),
            targetProjectPath: $this->nullableParagraph($sections['target project'] ?? ''),
            inScope: $this->listItems($sections['in scope'] ?? ''),
            outOfScope: $this->listItems($sections['out of scope'] ?? ''),
            acceptanceCriteria: $this->listItems($sections['acceptance criteria'] ?? ''),
            constraints: $this->listItems($sections['constraints'] ?? ''),
            openQuestions: $this->listItems($sections['open questions'] ?? ''),
        );

        return StageResult::succeeded(
            payload: $brief->toArray(),
            evidenceRefs: ['requirement.file'],
        );
    }

    private function parseTitle(string $contents): string
    {
        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $line = trim($line);

            if (str_starts_with($line, '# ')) {
                return trim(substr($line, 2));
            }
        }

        return 'Untitled Requirement';
    }

    /**
     * @return array<string, string>
     */
    private function parseSections(string $contents): array
    {
        $sections = [];
        $currentHeading = null;

        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $trimmedLine = trim($line);

            if (str_starts_with($trimmedLine, '## ')) {
                $currentHeading = strtolower(trim(substr($trimmedLine, 3)));
                $sections[$currentHeading] = '';

                continue;
            }

            if ($currentHeading !== null) {
                $sections[$currentHeading] .= $line."\n";
            }
        }

        return array_map(trim(...), $sections);
    }

    /**
     * @param  array<string, string>  $sections
     * @return list<string>
     */
    private function validationErrors(array $sections): array
    {
        $errors = [];

        foreach (['goal', 'acceptance criteria'] as $requiredSection) {
            if (! array_key_exists($requiredSection, $sections) || trim($sections[$requiredSection]) === '') {
                $errors[] = 'Missing required section: '.$this->displayHeading($requiredSection).'.';
            }
        }

        return $errors;
    }

    private function displayHeading(string $heading): string
    {
        return implode(' ', array_map(ucfirst(...), explode(' ', $heading)));
    }

    private function paragraph(string $contents): string
    {
        $lines = array_filter(array_map(trim(...), preg_split('/\R/', $contents) ?: []));

        return implode(' ', $lines);
    }

    private function nullableParagraph(string $contents): ?string
    {
        $paragraph = $this->paragraph($contents);

        return $paragraph === '' ? null : $paragraph;
    }

    /**
     * @return list<string>
     */
    private function listItems(string $contents): array
    {
        $items = [];

        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $line = trim($line);

            if (str_starts_with($line, '- ')) {
                $items[] = trim(substr($line, 2));
            }
        }

        if ($items !== []) {
            return $items;
        }

        $paragraph = $this->paragraph($contents);

        return $paragraph === '' ? [] : [$paragraph];
    }
}

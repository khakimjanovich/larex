<?php

namespace Tests\Feature\ArchEngine;

use App\ArchEngine\Stages\NormalizeRequirementStage;
use PHPUnit\Framework\Attributes\Test;
use Random\RandomException;
use Tests\TestCase;

class NormalizeRequirementStageTest extends TestCase
{
    #[Test]
    public function it_normalizes_a_requirement_markdown_file_into_a_requirement_brief(): void
    {
        $requirementPath = $this->makeRequirement(<<<'MARKDOWN'
# REQ-0099: Import GitHub Milestones

## Goal

Read GitHub milestones as external planning input.

## Target Project

/tmp/example-target

## In Scope

- Read milestones
- Read issues for a milestone

## Out of Scope

- Write to GitHub
- Apply code changes

## Acceptance Criteria

- Milestones are represented as evidence.
- Issues can become requirement drafts.

## Constraints

- Read-only GitHub access.
- No package installs.

## Open Questions

- Which token scope is required?
MARKDOWN);

        $result = (new NormalizeRequirementStage($requirementPath))->handle();

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('succeeded', $result->status);
        $this->assertSame('requirement-brief-v1', $result->payload['schema_version']);
        $this->assertSame('REQ-0099: Import GitHub Milestones', $result->payload['title']);
        $this->assertSame('Read GitHub milestones as external planning input.', $result->payload['goal']);
        $this->assertSame('/tmp/example-target', $result->payload['target_project_path']);
        $this->assertSame(['Read milestones', 'Read issues for a milestone'], $result->payload['in_scope']);
        $this->assertSame(['Write to GitHub', 'Apply code changes'], $result->payload['out_of_scope']);
        $this->assertSame([
            'Milestones are represented as evidence.',
            'Issues can become requirement drafts.',
        ], $result->payload['acceptance_criteria']);
        $this->assertSame(['Read-only GitHub access.', 'No package installs.'], $result->payload['constraints']);
        $this->assertSame(['Which token scope is required?'], $result->payload['open_questions']);
        $this->assertContains('requirement.file', $result->evidenceRefs);
    }

    #[Test]
    public function it_blocks_when_the_requirement_file_is_missing(): void
    {
        $result = (new NormalizeRequirementStage('/missing/REQ-0000.md'))->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertFalse($result->recoverable);
        $this->assertSame(['Requirement file does not exist.'], $result->errors);
        $this->assertContains('requirement.missing_file', $result->evidenceRefs);
    }

    #[Test]
    public function it_blocks_when_required_sections_are_missing(): void
    {
        $requirementPath = $this->makeRequirement(<<<'MARKDOWN'
# REQ-0100: Incomplete Requirement

## Goal

This requirement has no acceptance criteria.
MARKDOWN);

        $result = (new NormalizeRequirementStage($requirementPath))->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertContains('Missing required section: Acceptance Criteria.', $result->errors);
    }

    /**
     * @throws RandomException
     */
    private function makeRequirement(string $contents): string
    {
        $directory = sys_get_temp_dir().'/larex-requirement-'.bin2hex(random_bytes(8));

        mkdir($directory, 0777, true);

        $path = $directory.'/REQ-test.md';

        file_put_contents($path, $contents);

        return $path;
    }
}

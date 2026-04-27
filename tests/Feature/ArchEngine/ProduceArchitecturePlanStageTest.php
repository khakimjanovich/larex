<?php

namespace Tests\Feature\ArchEngine;

use App\Ai\Agents\ArchitecturePlannerAgent;
use App\ArchEngine\DTO\RequirementBrief;
use App\ArchEngine\DTO\TargetProjectFacts;
use App\ArchEngine\Stages\ProduceArchitecturePlanStage;
use App\ArchEngine\TargetProject\TargetProject;
use App\ArchEngine\Tools\CodebaseReader;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProduceArchitecturePlanStageTest extends TestCase
{
    #[Test]
    public function it_produces_an_architecture_plan_from_a_requirement_and_codebase(): void
    {
        ArchitecturePlannerAgent::fake();

        $result = $this->makeStage(targetProjectPath: base_path())->handle();

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('architecture-plan-v1', $result->payload['schema_version']);
        $this->assertArrayHasKey('title', $result->payload);
        $this->assertArrayHasKey('summary', $result->payload);
        $this->assertArrayHasKey('proposed_changes', $result->payload);
        $this->assertArrayHasKey('risks', $result->payload);
        $this->assertArrayHasKey('open_questions', $result->payload);
        $this->assertContains('codebase.snapshot', $result->evidenceRefs);
        $this->assertContains('requirement.brief', $result->evidenceRefs);

        ArchitecturePlannerAgent::assertPrompted(fn ($prompt) => $prompt->contains('Acceptance Criteria'));
    }

    #[Test]
    public function it_blocks_when_requirement_has_no_target_project_path(): void
    {
        ArchitecturePlannerAgent::fake();

        $result = $this->makeStage(targetProjectPath: null)->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertStringContainsString('targetProjectPath', $result->errors[0]);

        ArchitecturePlannerAgent::assertNeverPrompted();
    }

    #[Test]
    public function it_includes_project_facts_in_the_prompt(): void
    {
        ArchitecturePlannerAgent::fake();

        $this->makeStage(targetProjectPath: base_path())->handle();

        ArchitecturePlannerAgent::assertPrompted(fn ($prompt) => $prompt->contains('laravel_app'));
    }

    private function makeStage(?string $targetProjectPath): ProduceArchitecturePlanStage
    {
        $requirement = new RequirementBrief(
            schemaVersion: 'requirement-brief-v1',
            title: 'REQ-0007: Test Feature',
            goal: 'Build a test feature for verification.',
            targetProjectPath: $targetProjectPath,
            inScope: ['Build the feature', 'Write tests'],
            outOfScope: ['UI changes'],
            acceptanceCriteria: ['Feature works end-to-end', 'Tests pass'],
            constraints: ['No new packages'],
            openQuestions: [],
        );

        $facts = new TargetProjectFacts(
            schemaVersion: 'target-project-facts-v1',
            path: base_path(),
            projectType: TargetProject::LaravelApp,
            composerName: 'laravel/laravel',
            phpConstraint: '^8.4',
            laravelVersion: '^13.0',
            installedPackages: ['laravel/ai', 'spatie/laravel-data'],
            devTools: ['laravel/pint', 'phpunit/phpunit'],
            testRunner: 'phpunit',
            evidenceRefs: ['composer.json'],
        );

        return new ProduceArchitecturePlanStage(
            requirement: $requirement,
            facts: $facts,
            reader: new CodebaseReader,
            agent: new ArchitecturePlannerAgent,
        );
    }
}

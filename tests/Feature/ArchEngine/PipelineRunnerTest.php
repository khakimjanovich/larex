<?php

namespace Tests\Feature\ArchEngine;

use App\ArchEngine\Pipeline\PipelineRunner;
use App\ArchEngine\Pipeline\PipelineRunState;
use App\ArchEngine\Pipeline\StageContract;
use App\ArchEngine\Pipeline\StageResult;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PipelineRunnerTest extends TestCase
{
    #[Test]
    public function it_runs_stages_in_order_and_records_results(): void
    {
        $executionOrder = [];

        $state = (new PipelineRunner)->run([
            new RecordingStage('normalize_requirement', $executionOrder),
            new RecordingStage('inspect_target', $executionOrder),
        ]);

        $this->assertSame(['normalize_requirement', 'inspect_target'], $executionOrder);
        $this->assertCount(2, $state->stageResults());
        $this->assertSame('succeeded', $state->resultFor('normalize_requirement')?->status);
        $this->assertSame('inspect_target', $state->resultFor('inspect_target')?->payload['stage']);
        $this->assertFalse($state->hasStopped());
    }

    #[Test]
    public function it_stops_after_a_non_recoverable_failed_stage(): void
    {
        $executionOrder = [];

        $state = (new PipelineRunner)->run([
            new RecordingStage('normalize_requirement', $executionOrder),
            new FailingStage('risk_audit', $executionOrder, recoverable: false),
            new RecordingStage('patch_plan', $executionOrder),
        ]);

        $this->assertSame(['normalize_requirement', 'risk_audit'], $executionOrder);
        $this->assertTrue($state->hasStopped());
        $this->assertSame('risk_audit', $state->stoppedStage());
        $this->assertSame(['missing evidence'], $state->resultFor('risk_audit')?->errors);
        $this->assertNull($state->resultFor('patch_plan'));
    }

    #[Test]
    public function it_continues_after_a_recoverable_failed_stage(): void
    {
        $executionOrder = [];

        $state = (new PipelineRunner)->run([
            new FailingStage('standards_grounding', $executionOrder, recoverable: true),
            new RecordingStage('architecture_plan', $executionOrder),
        ]);

        $this->assertSame(['standards_grounding', 'architecture_plan'], $executionOrder);
        $this->assertFalse($state->hasStopped());
        $this->assertTrue($state->resultFor('standards_grounding')?->recoverable);
        $this->assertSame('succeeded', $state->resultFor('architecture_plan')?->status);
    }

    #[Test]
    public function stage_result_contains_payload_evidence_warnings_and_errors(): void
    {
        $result = StageResult::failed(
            payload: ['component' => 'TargetProjectDetector'],
            evidenceRefs: ['target.composer_json'],
            warnings: ['assumption: package version from constraint'],
            errors: ['missing approval'],
            recoverable: true,
        );

        $this->assertSame('failed', $result->status);
        $this->assertSame(['component' => 'TargetProjectDetector'], $result->payload);
        $this->assertSame(['target.composer_json'], $result->evidenceRefs);
        $this->assertSame(['assumption: package version from constraint'], $result->warnings);
        $this->assertSame(['missing approval'], $result->errors);
        $this->assertTrue($result->recoverable);
        $this->assertFalse($result->isSuccessful());
    }
}

final class RecordingStage implements StageContract
{
    /**
     * @param  list<string>  $executionOrder
     */
    public function __construct(
        private readonly string $name,
        private array &$executionOrder,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function handle(PipelineRunState $state): StageResult
    {
        $this->executionOrder[] = $this->name;

        return StageResult::succeeded(
            payload: ['stage' => $this->name],
            evidenceRefs: ['test.'.$this->name],
            warnings: [],
        );
    }
}

final class FailingStage implements StageContract
{
    /**
     * @param  list<string>  $executionOrder
     */
    public function __construct(
        private readonly string $name,
        private array &$executionOrder,
        private readonly bool $recoverable,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function handle(PipelineRunState $state): StageResult
    {
        $this->executionOrder[] = $this->name;

        return StageResult::failed(
            payload: ['stage' => $this->name],
            evidenceRefs: ['test.'.$this->name],
            warnings: ['warning from '.$this->name],
            errors: ['missing evidence'],
            recoverable: $this->recoverable,
        );
    }
}

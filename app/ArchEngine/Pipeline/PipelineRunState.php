<?php

namespace App\ArchEngine\Pipeline;

final class PipelineRunState
{
    /**
     * @var array<string, StageResult>
     */
    private array $stageResults = [];

    private ?string $stoppedStage = null;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        private readonly array $context = [],
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function make(array $context = []): self
    {
        return new self($context);
    }

    public function recordStageResult(string $stageName, StageResult $result): void
    {
        $this->stageResults[$stageName] = $result;
    }

    /**
     * @return array<string, StageResult>
     */
    public function stageResults(): array
    {
        return $this->stageResults;
    }

    public function resultFor(string $stageName): ?StageResult
    {
        return $this->stageResults[$stageName] ?? null;
    }

    public function stopAt(string $stageName): void
    {
        $this->stoppedStage = $stageName;
    }

    public function hasStopped(): bool
    {
        return $this->stoppedStage !== null;
    }

    public function stoppedStage(): ?string
    {
        return $this->stoppedStage;
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return $this->context;
    }
}

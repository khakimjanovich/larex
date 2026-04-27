<?php

namespace App\ArchEngine\Pipeline;

interface StageContract
{
    public function name(): string;

    public function handle(PipelineRunState $state): StageResult;
}

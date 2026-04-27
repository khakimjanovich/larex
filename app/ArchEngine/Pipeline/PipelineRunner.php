<?php

namespace App\ArchEngine\Pipeline;

final class PipelineRunner
{
    /**
     * @param  iterable<StageContract>  $stages
     */
    public function run(iterable $stages, ?PipelineRunState $state = null): PipelineRunState
    {
        $state ??= PipelineRunState::make();

        foreach ($stages as $stage) {
            $result = $stage->handle($state);

            $state->recordStageResult($stage->name(), $result);

            if ($result->shouldStopPipeline()) {
                $state->stopAt($stage->name());

                break;
            }
        }

        return $state;
    }
}

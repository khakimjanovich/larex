# Current Task

## Requirement

REQ-0003: Pipeline Core.

## Goal

Create the core pipeline types that let Larex run stages in order and stop safely when a stage fails.

## Scope

Created:

- `app/ArchEngine/Pipeline/PipelineRunner.php`
- `app/ArchEngine/Pipeline/StageContract.php`
- `app/ArchEngine/Pipeline/StageResult.php`
- `app/ArchEngine/Pipeline/PipelineRunState.php`
- `tests/Feature/ArchEngine/PipelineRunnerTest.php`
- `docs/requirements/REQ-0003-pipeline-core.md`

## Out of Scope

- CLI commands
- approval gates
- patch planning
- target project file mutation
- Composer dependency changes

## Status

Implemented.

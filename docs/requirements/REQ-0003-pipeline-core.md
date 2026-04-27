# REQ-0003: Pipeline Core

## Status

Implemented.

## Goal

Create the core pipeline types that let Larex run stages in order and stop safely when a stage fails.

## Background

Larex must own the workflow. Agents and tools should not operate as free-floating implementation logic. Every meaningful operation should belong to a stage with structured input, structured output, evidence references, warnings, and errors.

## In Scope

- Create `PipelineRunner`.
- Create `StageContract`.
- Create `StageResult`.
- Create `PipelineRunState`.
- Run stages in order.
- Record each stage result.
- Stop on failed or blocked stages unless the result is recoverable.
- Add PHPUnit feature tests for the core pipeline behavior.

## Out of Scope

- CLI commands.
- Runtime artifact persistence.
- Reasoning providers.
- Approval gates.
- Target project mutation.
- Architecture, risk, or patch planning stages.

## Acceptance Criteria

- Pipeline can run multiple stages in order.
- Stage result contains status, payload, evidence refs, warnings, and errors.
- Failed stage stops the pipeline unless explicitly marked recoverable.
- Blocked stage stops the pipeline unless explicitly marked recoverable.
- Tests cover ordered execution, non-recoverable stop, recoverable continuation, and result structure.

## Evidence References

- `README.md`: defines REQ-0003 and pipeline-first architecture.
- `.larex/memory/architecture-rules.md`: requires stages, contracts, evidence, and validation boundaries.
- `.larex/session/codex-session-brief.md`: identifies REQ-0003 as the recommended next requirement.

## Unknowns

- Final persisted run artifact format.
- Whether stage payload validation belongs inside `PipelineRunner` or a later validator stage.
- Whether pipeline state should become immutable after artifact storage is introduced.

# Codex Session Brief

## Project

Larex is a Laravel-controlled architecture reasoning engine for Laravel applications, Laravel packages, and Composer-based PHP packages.

## Current State

- The repository currently appears to be a Laravel application skeleton.
- Durable project memory has been bootstrapped under `.larex/memory/`.
- REQ-0002 target project detection has been implemented as a pure PHP detector and DTO.
- No Larex CLI commands or runtime pipeline has been created yet.

## Operating Mode

Future Codex sessions should read `.larex/memory/` before proposing or implementing Larex features.

Do not rely on `AGENTS.md` as permanent product memory. It may provide session instructions, but Larex memory is under `.larex/memory/`.

## Immediate Next Step

Recommended next requirement: REQ-0003 pipeline core.

REQ-0003 should define and implement:

- `PipelineRunner`
- `StageContract`
- `StageResult`
- `PipelineRunState`
- focused tests for ordered stage execution and failure handling

## Unknowns

- Artifact run directory layout.
- Exact `larex` command registration strategy.
- Approval persistence mechanism.
- Whether target project facts should be persisted during REQ-0003 or deferred to CLI/artifact storage.

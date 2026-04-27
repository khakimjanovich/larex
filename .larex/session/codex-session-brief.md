# Codex Session Brief

## Project

Larex is a Laravel-controlled architecture reasoning engine for Laravel applications, Laravel packages, and Composer-based PHP packages.

## Current State

- The repository currently appears to be a Laravel application skeleton.
- Durable project memory has been bootstrapped under `.larex/memory/`.
- REQ-0002 target project detection has been implemented as a pure PHP detector and DTO.
- REQ-0003 pipeline core has been implemented with runner, stage contract, stage result, and run state.
- No Larex CLI commands have been created yet.

## Operating Mode

Future Codex sessions should read `.larex/memory/codex-bootstrap.md` first before proposing or implementing Larex features.

Do not rely on `AGENTS.md` as permanent product memory. It may provide session instructions, but Larex memory is under `.larex/memory/`.

## Immediate Next Step

Recommended next requirement: REQ-0004 CLI skeleton.

REQ-0004 should define and implement:

- `larex inspect` behavior, likely first as an Artisan command unless a bin wrapper is explicitly approved.
- current-directory target project inspection by default.
- explicit `--project=/path` target inspection.
- structured target project facts output.
- no target project mutation.

## Unknowns

- Artifact run directory layout.
- Exact `larex` command registration strategy.
- Approval persistence mechanism.
- Whether REQ-0004 should expose only `php artisan larex:inspect` first or also a top-level `larex` binary.

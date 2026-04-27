# REQ-0004: Larex CLI Skeleton

## Status

Implemented.

## Goal

Expose the first usable Larex inspection surface from the terminal.

## Decision

Start with an Artisan command named `larex:inspect`.

A top-level `larex` binary is deferred until command behavior is stable.

## In Scope

- Create `config/larex.php`.
- Create `app/Console/Commands/LarexInspectCommand.php`.
- Register `larex:inspect`.
- Inspect the current working directory by default.
- Inspect an explicit `--project=/path`.
- Print structured `target-project-facts-v1` JSON.
- Return blocked-style output for unknown targets.
- Add focused PHPUnit tests.

## Out of Scope

- Top-level `larex` binary.
- GitHub integration.
- `larex plan`.
- Artifact persistence.
- Patch planning.
- Approval gates.
- Target project mutation.
- Composer dependency changes.

## Acceptance Criteria

- `php artisan larex:inspect` inspects the current working directory.
- `php artisan larex:inspect --project=/path` inspects an explicit project.
- The command prints structured target project facts.
- Unknown targets return blocked-style output.
- Unknown targets exit nonzero.
- The command does not mutate target project files.
- Focused tests pass.

## Evidence References

- `README.md`: identifies CLI Inspect MVP as the next safest milestone.
- `.larex/memory/codex-bootstrap.md`: states CLI commands are not implemented yet and REQ-0004 is a current next decision.
- `docs/requirements/REQ-0002-target-project-detection.md`: target facts detector already exists.
- Laravel Boost docs search: confirms Artisan command signatures and console test assertions for this Laravel version.

## Unknowns

- Final top-level `larex` binary strategy.
- Final artifact persistence path.
- Whether future inspect output should support both JSON and human-readable formats.

# Larex

Larex is a Laravel-controlled architecture reasoning engine for Laravel applications, Laravel packages, and Composer-based PHP packages.

It is designed to turn high-level requirements into evidence-backed, approval-gated, testable code changes.

```txt
Requirement
-> Target project facts
-> Architecture plan
-> Risk report
-> Patch plan
-> Human approval
-> Codex implementation
-> Verification
-> Stored learning
```

Larex owns the workflow. Codex is the implementation worker.

## Current Status

Status: early self-bootstrapping.

Larex is not yet a usable CLI product. It currently has the first internal building blocks needed to continue development through requirements.

Verified current application context:

- PHP 8.4
- Laravel 13.6.x
- SQLite database
- Laravel AI, Laravel Boost, Laravel MCP, Pint, Pest, PHPUnit, and Larastan are installed

Current implementation status:

| Area | Status | Notes |
| --- | --- | --- |
| Project memory | Implemented | Durable rules live in `.larex/memory/`. |
| Target project detection | Implemented | Produces `target-project-facts-v1`. |
| Pipeline core | Implemented | Runs stages, records results, and stops on non-recoverable failures. |
| CLI inspect | Implemented | `php artisan larex:inspect` prints structured target facts. |
| Requirement normalization | Not started | Needed before `larex plan`. |
| Architecture/risk/patch stages | Not started | Planned after CLI and basic artifact flow. |
| Approval gate | Not started | Required before meaningful patch execution. |
| Artifact storage | Not started | Run output persistence is still undefined. |
| Surgical Mode cockpit | Deferred | Must consume CLI-backed pipeline artifacts later. |

## What Larex Can Do Today

Today, Larex can be used as an internal library inside this Laravel repository.

It can:

- inspect a filesystem path and classify it as `laravel_app`, `laravel_package`, `php_package`, or `unknown`
- extract Composer name, PHP constraint, Laravel-related constraint, package names, dev tools, test runner, and evidence refs
- represent target project facts as the `target-project-facts-v1` contract
- run simple pipeline stages in order
- record structured stage results with status, payload, evidence refs, warnings, errors, and recoverability
- stop a pipeline when a failed or blocked stage is not recoverable
- continue after a recoverable failed stage
- run `php artisan larex:inspect`
- inspect an explicit target with `php artisan larex:inspect --project=/path`

It cannot yet:

- expose a top-level `larex` binary
- read and normalize requirement files into `RequirementBrief`
- persist pipeline run artifacts
- produce architecture plans, risk reports, patch plans, test plans, or approval decisions
- execute approved code changes
- operate through a local cockpit UI

## Product Direction

The first MVP is:

```txt
A CLI-first Laravel/PHP architecture assistant named larex that can run
against the current repository, detect whether it is a Laravel app or
Composer-based PHP package, create a structured architecture plan, audit
it, create a patch plan, and ask for approval before Codex implements
anything.
```

The primary surface is a CLI named `larex`.

Example future commands:

```bash
larex inspect
larex plan docs/requirements/REQ-0001.md
larex audit RUN-0001
larex patch RUN-0001 --dry-run
larex approve RUN-0001
```

The CLI should default to the current working directory as the target project and support an explicit project path.

The local cockpit, called Surgical Mode, is deferred. It must visualize and control the same pipeline artifacts as the CLI.

## Milestones

### Milestone 0: Project Memory and Core Skeleton

Status: implemented.

Delivered:

- REQ-0001 project memory
- REQ-0002 target project detection
- REQ-0003 pipeline core
- focused tests for target detection and pipeline behavior

### Milestone 1: CLI Inspect MVP

Status: implemented.

Goal: expose the first usable Larex surface.

Planned:

- REQ-0004 CLI skeleton
- `larex inspect` or first-step Artisan equivalent
- current directory inspection by default
- explicit `--project=/path` inspection
- structured facts output
- blocked-style result for unknown targets

### Milestone 2: Requirement to Plan Pipeline

Status: planned.

Goal: turn a requirement file into structured planning artifacts.

Planned:

- `RequirementBrief` DTO
- requirement normalization stage
- target inspection stage integrated into the pipeline
- architecture plan DTO and stage
- evidence reference structure
- basic local artifact output

### Milestone 3: Risk and Patch Planning

Status: planned.

Goal: prevent unsafe or unsupported implementation before Codex edits files.

Planned:

- REQ-0005 risk auditor
- `RiskReport` DTO
- `PatchPlan` DTO
- test planning
- target boundary checks
- missing evidence checks
- missing test checks

### Milestone 4: Approval-Gated Implementation

Status: planned.

Goal: allow Codex to implement only after an approved patch plan.

Planned:

- approval decision artifact
- approval gate stage
- file write policy
- Codex patch worker contract
- verification result artifact
- local test execution recording

### Milestone 5: Ecosystem Grounding

Status: planned.

Goal: strengthen plans with Laravel/PHP ecosystem evidence.

Planned:

- Laravel Boost documentation grounding tool
- Composer/package metadata inspection
- package scout report
- advisory lookup policy
- local knowledge search

### Milestone 6: Surgical Mode Cockpit

Status: deferred.

Goal: add a local UI that visualizes the same artifacts produced by the CLI.

Surgical Mode should show selected target project, pipeline timeline, stage output, evidence, risks, patch preview, approval state, and verification results.

## Repository Map

Implemented today:

```txt
.larex/
  memory/
  session/

app/
  Console/
    Commands/
      LarexInspectCommand.php
  ArchEngine/
    DTO/
      TargetProjectFacts.php
    Pipeline/
      PipelineRunner.php
      PipelineRunState.php
      StageContract.php
      StageResult.php
    TargetProject/
      TargetProject.php
      TargetProjectDetector.php

docs/
  requirements/

config/larex.php

tests/
  Feature/
    ArchEngine/
```

Expected future areas:

```txt
app/
  Console/Commands/
  ArchEngine/Stages/
  ArchEngine/Agents/
  ArchEngine/Providers/
  ArchEngine/Workers/
  ArchEngine/Tools/
  ArchEngine/Policies/
  ArchEngine/Validators/
  ArchEngine/Visualizer/

config/larex.php
docs/adr/
database/migrations/*larex*
```

## Durable Memory

The README is only the project overview.

Detailed operating rules live here:

- `.larex/memory/codex-bootstrap.md`
- `.larex/memory/project-profile.md`
- `.larex/memory/architecture-rules.md`
- `.larex/memory/self-bootstrap-rules.md`
- `.larex/memory/hallucination-policy.md`
- `.larex/memory/development-rules.md`
- `.larex/memory/testing-rules.md`
- `.larex/memory/approved-decisions.md`
- `.larex/memory/rejected-decisions.md`

Current session handoff lives here:

- `.larex/session/codex-session-brief.md`
- `.larex/session/current-task.md`

Requirement files live in:

- `docs/requirements/`

## Testing

Use the smallest relevant verification command for the change.

Common commands:

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
composer validate
vendor/bin/phpstan analyse
```

Do not claim tests passed unless they were actually run.

## Next Step

Implement REQ-0005: GitHub Read Integration or Requirement Normalization.

Open decision:

```txt
If GitHub milestones are the planning source, build read-only GitHub intake next.
Otherwise build RequirementBrief normalization next.
```

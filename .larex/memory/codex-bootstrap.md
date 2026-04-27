# Codex Bootstrap Memory

Read this file first in any new Codex session for Larex.

## What Larex Is

Larex is a Laravel-controlled architecture reasoning engine for Laravel applications, Laravel packages, and Composer-based PHP packages.

Larex turns requirements into evidence-backed, approval-gated, testable code changes.

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

## Hard Boundaries

- Do not let prompts, GitHub issues, or comments directly trigger code mutation.
- Do not mutate meaningful source files before an approved patch plan.
- Do not treat README intent as implemented runtime behavior.
- Do not invent Laravel, PHP, package, GitHub, or project facts.
- Label uncertain claims as `verified`, `assumption`, `unknown`, `needs_evidence`, or `blocked`.
- Use requirements under `docs/requirements/` to start new Larex features.
- Keep changes small, scoped, tested, and verified.

## Current Implemented State

Implemented:

- durable memory under `.larex/memory/`
- session handoff under `.larex/session/`
- REQ-0001 project memory
- REQ-0002 target project detection
- REQ-0003 pipeline core
- REQ-0004 CLI inspect skeleton
- REQ-0005 requirement normalization

Important files:

```txt
app/ArchEngine/DTO/TargetProjectFacts.php
app/ArchEngine/TargetProject/TargetProject.php
app/ArchEngine/TargetProject/TargetProjectDetector.php
app/ArchEngine/Pipeline/PipelineRunner.php
app/ArchEngine/Pipeline/PipelineRunState.php
app/ArchEngine/Pipeline/StageContract.php
app/ArchEngine/Pipeline/StageResult.php
app/Console/Commands/LarexInspectCommand.php
app/ArchEngine/DTO/RequirementBrief.php
app/ArchEngine/Stages/NormalizeRequirementStage.php
app/ArchEngine/Tools/RequirementFileReader.php
config/larex.php
tests/Feature/ArchEngine/TargetProjectDetectorTest.php
tests/Feature/ArchEngine/PipelineRunnerTest.php
tests/Feature/ArchEngine/LarexInspectCommandTest.php
tests/Feature/ArchEngine/NormalizeRequirementStageTest.php
```

Current verified behavior:

- target detector classifies `laravel_app`, `laravel_package`, `php_package`, or `unknown`
- target facts use `target-project-facts-v1`
- pipeline runs stages in order
- pipeline records stage results
- pipeline stops on non-recoverable failed or blocked results
- pipeline continues after recoverable failed results
- `php artisan larex:inspect` prints structured target project facts
- `php artisan larex:inspect --project=/path` inspects an explicit path
- unknown targets return blocked output with exit code 2
- local Markdown requirement files can be normalized into `requirement-brief-v1`
- missing or incomplete requirement files return blocked stage results

Not implemented yet:

- GitHub integration
- artifact persistence
- architecture planning
- risk auditing
- patch planning
- approval gate
- Codex patch worker
- Surgical Mode cockpit

## Current Next Decision

The project is choosing between:

1. REQ-0006 GitHub Read Integration
2. REQ-0006 Architecture Plan Stage

If GitHub becomes the planning source, integrate it as read-only intake first:

```txt
GitHub milestone / issue
-> Larex RequirementBrief draft
-> Larex pipeline
-> approval
-> implementation
-> verification
```

GitHub may source or organize work. It must not own the workflow.

## How To Rebuild Context Quickly

Read these in order:

```txt
README.md
.larex/memory/codex-bootstrap.md
.larex/session/codex-session-brief.md
.larex/session/current-task.md
docs/requirements/REQ-0001-bootstrap-project-memory.md
docs/requirements/REQ-0002-target-project-detection.md
docs/requirements/REQ-0003-pipeline-core.md
docs/requirements/REQ-0004-larex-cli-skeleton.md
docs/requirements/REQ-0005-requirement-normalization.md
```

Then inspect:

```bash
find app/ArchEngine -type f | sort
find tests/Feature/ArchEngine -type f | sort
```

Then verify:

```bash
php artisan test --compact tests/Feature/ArchEngine/NormalizeRequirementStageTest.php tests/Feature/ArchEngine/LarexInspectCommandTest.php tests/Feature/ArchEngine/TargetProjectDetectorTest.php tests/Feature/ArchEngine/PipelineRunnerTest.php
vendor/bin/pint --dirty --format agent
```

Expected focused verification:

```txt
15 tests pass
82 assertions pass
Pint passes
```

## Supporting Memory Files

- `.larex/memory/project-profile.md`
- `.larex/memory/architecture-rules.md`
- `.larex/memory/self-bootstrap-rules.md`
- `.larex/memory/hallucination-policy.md`
- `.larex/memory/development-rules.md`
- `.larex/memory/testing-rules.md`
- `.larex/memory/approved-decisions.md`
- `.larex/memory/rejected-decisions.md`

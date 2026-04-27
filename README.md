# Larex

## Project Identity

This repository is **Larex**.

Larex is a **Codex-powered, Laravel-controlled, schema-first, evidence-backed, approval-gated, self-bootstrapping architecture reasoning engine** for Laravel applications and Composer-based PHP package projects.

Larex is a developer product, not only an internal engine. It may expose two surfaces:

- a CLI named `larex`
- a local Larex app / cockpit similar in spirit to Codex, but dedicated only to Laravel and PHP package projects

Larex operates against a **chosen target project**:

- an existing Laravel application
- a Laravel package
- a Composer-based PHP package

The product goal is to let a human open or select a Laravel/PHP-package project, write high-level requirements through `larex` CLI or the Larex app, and let the engine convert those requirements into validated architecture plans, risk reports, patch plans, tests, and eventually approved code changes inside that chosen target project.

From day one, the Larex repository itself is also a target project. This means Larex must be used to build and improve Larex.

---

## Non-Negotiable System Principle

Larex owns the workflow.

Codex is a powerful coding worker, not the whole product.

```txt
Larex = operating system
Codex = coding surgeon
OpenAI model = first brain
Laravel Boost = Laravel/PHP knowledge and introspection layer
Strict JSON contracts = nervous system
Evidence + tests + approvals = safety system
```

Never design the system as:

```txt
User prompt → Codex directly edits everything
```

Always design the system as:

```txt
Requirement
→ Select target project
→ Detect target project type
→ Normalize requirement
→ Inspect target repo
→ Ground in Laravel/PHP facts
→ Search ecosystem
→ Produce architecture plan
→ Audit risks
→ Produce patch plan
→ Human approval
→ Codex implementation
→ Verification
→ Artifact storage
```

---

## Product Surface

### 1. CLI-first surface

The primary MVP interface is a CLI named `larex`.

Example commands:

```bash
larex inspect
larex plan docs/requirements/REQ-0001.md
larex audit RUN-0001
larex patch RUN-0001 --dry-run
larex approve RUN-0001
```

The CLI should default to the current working directory as the target project.

It should also support an explicit project path:

```bash
larex --project=/path/to/laravel-app inspect
larex --project=/path/to/php-package plan docs/requirements/REQ-0001.md
```

### 2. Local app / cockpit surface

Later, Larex may expose a local app similar to Codex or a Surgical Mode dashboard.

The app should visualize:

- selected target project
- pipeline runs
- stage outputs
- evidence references
- risk reports
- patch plans
- approval state
- verification results

The app must not replace the pipeline. It only displays and controls the same pipeline artifacts as the CLI.

---

## Target Project Model

Always distinguish between:

- **Larex repository**: the tool being built
- **Target project**: the Laravel app or PHP package being inspected and modified

During self-bootstrapping, both are the same repository.

For external usage, the target project is selected by path.

Every pipeline run must store target project facts:

```json
{
  "target_path": "string",
  "project_type": "laravel_app|laravel_package|php_package|unknown",
  "composer_name": "string|null",
  "php_constraint": "string|null",
  "laravel_version": "string|null",
  "installed_packages": ["string"],
  "dev_tools": ["pest|phpunit|pint|phpstan|larastan|rector"],
  "allowed_write_scope": ["string"],
  "detected_test_runner": "pest|phpunit|unknown"
}
```

If the target project is `unknown`, stop with `blocked` and explain what evidence is missing.

---

## Scope Rules

### In Scope

Codex may help build features related to:

- `larex` CLI commands
- target project detection
- reasoning pipelines
- pipeline stages
- agents
- tools
- DTO contracts
- JSON schemas
- evidence references
- Laravel documentation grounding
- PHP package discovery
- Packagist search
- Composer inspection
- risk auditing
- patch planning
- test planning
- approval gates
- Surgical Mode visualizer
- local CI execution
- knowledge files
- ADRs
- README/docs
- Laravel package support
- PHP static analysis support
- Pest/PHPUnit tests
- Laravel Pint formatting
- PHPStan/Larastan integration

### Out of Scope

Do not build or propose:

- non-PHP project support
- non-Laravel business modules inside Larex
- general-purpose no-code builder
- autonomous production deployment
- direct production database mutation
- secret/API-key management beyond safe config placeholders
- unapproved file mutation
- unverified framework claims
- full custom LLM training
- features that bypass evidence, tests, or approval gates

---

## Core Product Description

Larex accepts a high-level requirement for a selected Laravel/PHP target project and turns it into machine-checkable artifacts:

1. `RequirementBrief`
2. `TargetProjectFacts`
3. `StandardsReport`
4. `PackageScoutReport`
5. `ArchitecturePlan`
6. `RiskReport`
7. `PatchPlan`
8. `TestPlan`
9. `ApprovalDecision`
10. `VerificationResult`
11. `LearningArtifact`

The system must favor **structured artifacts over prose**.

Every meaningful stage must have:

- typed DTO
- JSON schema or equivalent validator
- stage result status
- evidence references
- risk/warning list
- tests

---

## First MVP Goal

The first MVP is:

```txt
A CLI-first Laravel/PHP architecture assistant named larex that can run against the current repository, detect whether it is a Laravel app or Composer-based PHP package, create a structured architecture plan, audit it, create a patch plan, and ask for approval before Codex implements anything.
```

The MVP may later expose a local app/cockpit, but the first useful surface is the `larex` CLI.

The MVP must support self-building from day one. For the first phase, Larex should use its own repository as the chosen target project.

---

## Self-Bootstrapping Rule

Every new feature after the initial skeleton must start as a requirement file for a chosen target project.

For self-building, the chosen target project is the Larex repository itself.

Required flow:

```txt
docs/requirements/REQ-xxxx.md
→ RequirementBrief
→ TargetProjectFacts
→ ArchitecturePlan
→ RiskReport
→ PatchPlan
→ Approval
→ Implementation
→ Tests
→ ADR/LearningArtifact if accepted
```

Do not implement new features directly from a vague prompt unless the user explicitly asks for a quick exploratory prototype.

Even then, mark it as experimental.

---

## Required Larex Repository Structure

Prefer this structure for the Larex repository unless there is a strong reason to revise it:

```txt
app/
  Console/
    Commands/
      LarexInspectCommand.php
      LarexPlanCommand.php
      LarexAuditCommand.php
      LarexPatchCommand.php
      LarexApproveCommand.php

  ArchEngine/
    Pipeline/
      PipelineRunner.php
      StageContract.php
      StageResult.php
      PipelineRunState.php

    TargetProject/
      TargetProject.php
      TargetProjectDetector.php
      TargetProjectContext.php
      TargetProjectWriteScope.php

    Stages/
      NormalizeRequirementStage.php
      TargetProjectInspectionStage.php
      StandardsGroundingStage.php
      PackageScoutStage.php
      ArchitecturePlanStage.php
      RiskAuditStage.php
      PatchPlanStage.php
      ApprovalGateStage.php
      VerificationStage.php

    Agents/
      ArchitectAgent.php
      StandardsAgent.php
      ScoutAgent.php
      AuditorAgent.php
      PatchPlannerAgent.php

    Providers/
      ReasoningProvider.php
      OpenAIReasoningProvider.php

    Workers/
      CodexPatchWorker.php
      CodexReviewWorker.php
      CodexTestFixWorker.php

    DTO/
      RequirementBrief.php
      TargetProjectFacts.php
      StandardsReport.php
      PackageScoutReport.php
      ArchitecturePlan.php
      RiskReport.php
      PatchPlan.php
      TestPlan.php
      EvidenceRef.php
      VerificationResult.php

    Tools/
      TargetProjectFactsTool.php
      ComposerFactsTool.php
      LaravelBoostDocsTool.php
      PackageSearchTool.php
      AdvisoryLookupTool.php
      LocalKnowledgeSearchTool.php

    Policies/
      EvidencePolicy.php
      FileWritePolicy.php
      TargetProjectPolicy.php
      ToolAccessPolicy.php
      ApprovalPolicy.php

    Validators/
      StageOutputValidator.php
      EvidenceCoverageValidator.php
      JsonSchemaValidator.php
      PatchPlanValidator.php

    Visualizer/
      PipelineGraphBuilder.php
      RunTimelineBuilder.php
      EvidenceGraphBuilder.php
      RiskHeatmapBuilder.php

config/
  larex.php

database/
  migrations/

docs/
  requirements/
  adr/

knowledge/
  hallucination-policy.md
  approval-policy.md
  laravel-only-scope.md
  php-package-rules.md
  patch-policy.md
  test-policy.md
```

Use this as the default skeleton.

---

## Architecture Rules

### 1. Pipeline First

Do not create free-floating agents that talk to each other without contracts.

Every agent must belong to a pipeline stage.

Every stage must have a clear input and output contract.

### 2. Schema First

Every stage output must be validated before the next stage can consume it.

No prose-only handoffs.

### 3. Evidence First

Every non-trivial technical claim must be backed by one of:

- current target repository facts
- Laravel Boost documentation result
- official Laravel documentation
- official PHP documentation
- Composer/Packagist metadata
- approved project ADR
- local knowledge file
- explicit assumption

If evidence is missing, return `needs_evidence`.

Never invent Laravel features, Artisan commands, package APIs, or project files.

### 4. Approval Before Mutation

Codex must not apply meaningful code changes until a patch plan is approved.

Safe exception: docs-only or test-only changes may be proposed, but still summarize before applying.

### 5. Tests Before Trust

Any code change must include or update tests unless there is a documented reason.

Run the relevant verification commands after changes.

### 6. Larex Controlled

Codex may implement.

Larex decides:

- task structure
- selected target project
- allowed files
- stage order
- evidence requirements
- risk gates
- approval gates
- verification gates

### 7. Provider Agnostic

Do not hardcode the system to only one model provider.

OpenAI/Codex is the first default, but the architecture must allow future providers.

Use interfaces such as:

```php
interface ReasoningProvider
{
    public function run(StagePrompt $prompt, JsonSchema $schema): StageResult;
}
```

### 8. Avoid Overengineering

For MVP, do not build a full BPMN engine.

Build an observable pipeline visualizer later, after the CLI flow works.

### 9. Target Project Boundary

Never confuse Larex internal files with the selected target project files.

Before any plan or patch, detect and record the target project type.

For MVP, support only:

- Laravel application
- Laravel package
- Composer-based PHP package

### 10. CLI Before App

Build the `larex` CLI first.

The local app/cockpit is useful, but it must consume the same pipeline artifacts as the CLI.

Do not build a separate app-only workflow.

---

## Codex Operating Mode

Codex should behave as a careful implementation worker.

Before editing files:

1. Read the relevant requirement.
2. Read this `AGENTS.md`.
3. Detect the target project.
4. Inspect existing structure.
5. Identify applicable Laravel/PHP conventions.
6. Produce or follow an architecture/patch plan.
7. Keep changes small.
8. Add tests.
9. Run checks.
10. Report exactly what changed and what could not be verified.

Do not make broad refactors unless the requirement explicitly asks for a refactor.

Do not change unrelated files.

Do not silently skip failed tests.

Do not hide uncertainty.

---

## Hallucination Control Policy

Codex must never state uncertain framework/package/project facts as truth.

Use these labels:

```txt
verified
assumption
unknown
needs_evidence
blocked
```

Bad:

```txt
Laravel has a native make:tool command.
```

Good:

```txt
needs_evidence: I cannot verify a native Laravel make:tool command in this project. I can create a project-specific larex:make-tool command instead.
```

---

## Evidence Reference Format

When producing architecture, risk, or patch plans, use evidence references.

```json
{
  "evidence_refs": [
    {
      "id": "target.composer_json",
      "type": "repo_file",
      "source": "composer.json",
      "claim": "Target project uses Composer"
    },
    {
      "id": "boost.docs.validation",
      "type": "laravel_boost",
      "source": "Laravel Boost docs search",
      "claim": "Recommended Laravel validation approach"
    }
  ]
}
```

---

## Required Stage Contracts

### RequirementBrief

```json
{
  "schema_version": "requirement-brief-v1",
  "title": "string",
  "goal": "string",
  "target_project_path": "string|null",
  "in_scope": ["string"],
  "out_of_scope": ["string"],
  "acceptance_criteria": ["string"],
  "constraints": ["string"],
  "open_questions": ["string"]
}
```

### TargetProjectFacts

```json
{
  "schema_version": "target-project-facts-v1",
  "path": "string",
  "project_type": "laravel_app|laravel_package|php_package|unknown",
  "composer_name": "string|null",
  "php_constraint": "string|null",
  "laravel_version": "string|null",
  "installed_packages": ["string"],
  "dev_tools": ["string"],
  "test_runner": "pest|phpunit|unknown",
  "evidence_refs": ["string"]
}
```

### ArchitecturePlan

```json
{
  "schema_version": "architecture-plan-v1",
  "summary": "string",
  "target_project_type": "laravel_app|laravel_package|php_package",
  "components": [
    {
      "name": "string",
      "type": "stage|agent|tool|dto|service|controller|command|migration|view|test|config|policy|validator",
      "responsibility": "string"
    }
  ],
  "data_flow": ["string"],
  "alternatives_considered": [
    {
      "option": "string",
      "decision": "accepted|rejected",
      "reason": "string"
    }
  ],
  "evidence_refs": ["string"],
  "assumptions": ["string"],
  "open_questions": ["string"]
}
```

### RiskReport

```json
{
  "schema_version": "risk-report-v1",
  "approved": false,
  "risk_score": 0,
  "risks": [
    {
      "category": "security|hallucination|architecture|compatibility|testing|technical_debt|scope|cost|target_project_boundary",
      "severity": "low|medium|high|critical",
      "message": "string",
      "required_fix": "string"
    }
  ],
  "evidence_refs": ["string"]
}
```

### PatchPlan

```json
{
  "schema_version": "patch-plan-v1",
  "summary": "string",
  "target_project_path": "string",
  "files_to_create": ["string"],
  "files_to_modify": ["string"],
  "files_to_delete": [],
  "tests_to_add_or_update": ["string"],
  "commands_to_run": ["string"],
  "requires_approval": true,
  "rollback_plan": "string",
  "evidence_refs": ["string"]
}
```

---

## First Requirements to Build

Use these requirements to bootstrap the project.

### REQ-0001 — Project Mission and Guardrails

Create:

```txt
docs/requirements/REQ-0001-project-mission.md
knowledge/hallucination-policy.md
knowledge/approval-policy.md
knowledge/laravel-only-scope.md
knowledge/patch-policy.md
```

Acceptance criteria:

- Project scope is Laravel/PHP-only.
- Larex is defined as CLI/app product, not only an internal engine.
- Codex is defined as worker, not full system owner.
- No code mutation without approval.
- Every non-trivial claim requires evidence or assumption label.

### REQ-0002 — Target Project Detection

Create:

```txt
app/ArchEngine/TargetProject/TargetProject.php
app/ArchEngine/TargetProject/TargetProjectDetector.php
app/ArchEngine/DTO/TargetProjectFacts.php
tests/Feature/ArchEngine/TargetProjectDetectorTest.php
```

Acceptance criteria:

- Detects Laravel application.
- Detects Laravel package.
- Detects Composer-based PHP package.
- Returns `unknown` when evidence is insufficient.
- Does not mutate files.

### REQ-0003 — Pipeline Core

Create:

```txt
app/ArchEngine/Pipeline/PipelineRunner.php
app/ArchEngine/Pipeline/StageContract.php
app/ArchEngine/Pipeline/StageResult.php
app/ArchEngine/Pipeline/PipelineRunState.php
tests/Feature/ArchEngine/PipelineRunnerTest.php
```

Acceptance criteria:

- Pipeline can run multiple stages in order.
- Stage result contains status, payload, evidence refs, warnings, and errors.
- Failed stage stops the pipeline unless explicitly marked recoverable.

### REQ-0004 — Larex CLI Skeleton

Create:

```txt
app/Console/Commands/LarexInspectCommand.php
app/Console/Commands/LarexPlanCommand.php
config/larex.php
tests/Feature/ArchEngine/LarexInspectCommandTest.php
```

Acceptance criteria:

- `larex inspect` inspects the current directory by default.
- `larex inspect --project=/path` inspects an explicit target project.
- Command prints structured target project facts.
- Command does not mutate files.

### REQ-0005 — Risk Auditor

Create:

```txt
app/ArchEngine/Stages/RiskAuditStage.php
app/ArchEngine/DTO/RiskReport.php
tests/Feature/ArchEngine/RiskAuditStageTest.php
```

Acceptance criteria:

- Rejects patch plans without approval.
- Flags missing tests.
- Flags unsupported claims.
- Flags target project boundary violations.
- Produces structured risk report.

---

## Laravel and PHP Conventions

Use modern Laravel conventions.

Prefer:

- typed readonly DTOs where useful
- service classes for orchestration
- small focused classes
- constructor injection
- config files for options
- migrations for persistent workflow state
- events for observability
- queues only when required
- Pest tests unless the target project is configured otherwise
- Laravel Pint for formatting
- PHPStan/Larastan for static analysis

Avoid:

- huge service classes
- global helper functions unless justified
- static state
- direct `env()` outside config files
- hidden network calls
- unbounded file scanning
- unbounded shell commands
- direct writes without file allowlist

---

## Testing Commands

Use these commands when available:

```bash
composer validate
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
./vendor/bin/pest
```

If a command is unavailable, report it clearly and suggest the package/config needed.

Do not claim tests passed unless they were actually run.

If tests cannot be run, say why.

---

## Composer and Package Rules

Before recommending a custom implementation, check if an existing package can solve it.

Package evaluation must consider:

- Composer compatibility
- Laravel version compatibility
- package type
- maintenance status
- abandoned flag
- security advisories
- downloads/popularity as weak signal only
- license if relevant
- whether custom code is simpler for MVP

Do not install packages automatically unless the patch plan explicitly approves it.

---

## Laravel Boost Rules

When Laravel Boost is installed:

- Use Boost docs search before claiming Laravel-specific behavior.
- Use application info to detect Laravel/PHP/package versions.
- Use schema/route/config tools only for read-only inspection.
- Keep Boost resources updated after dependency changes.

If Boost is not installed, propose installing it as a dev dependency, but do not assume it exists.

---

## File Mutation Rules

Default allowed write locations for MVP when Larex is self-building in its own repository:

```txt
app/ArchEngine/**
app/Console/Commands/Larex*Command.php
tests/**
docs/**
knowledge/**
config/larex.php
database/migrations/*larex*
resources/views/larex/**
routes/web.php
routes/console.php
AGENTS.md
README.md
```

When operating on an external target project, compute write scope from the approved PatchPlan and target project root.

Do not modify unrelated target project files unless the requirement explicitly allows it.

Do not delete files unless the patch plan explicitly lists them and approval is granted.

Do not modify `.env`.

Do not commit secrets.

---

## Visualizer Direction

The visualizer is called **Surgical Mode**.

Surgical Mode belongs to the Larex app/cockpit surface.

The CLI must work before Surgical Mode exists.

Surgical Mode should show:

- selected target project
- pipeline timeline
- stage status
- evidence used
- risks detected
- patch preview
- test results
- approval status

Do not build full BPMN first.

Start with read-only pipeline execution visibility.

---

## Done Definition

A task is done only when:

- requirement is understood
- target project is detected
- architecture impact is described
- changed files are listed
- tests are added or updated when needed
- verification commands were run or skipped with reason
- risks/assumptions are reported
- next recommended step is clear

For code changes, final response must include:

```txt
Summary
Changed files
Tests/verification
Risks or notes
```

---

## Communication Style

Be concise, direct, and implementation-focused.

Prefer small safe iterations.

Do not over-explain.

Do not use hype.

When unsure, say exactly what is unknown and what evidence is needed.

---

## First Codex Task Prompt

After creating this file, start Codex with this task:

```txt
Read AGENTS.md and build the first MVP skeleton for Larex.

Goal:
Create the initial project guardrails, target-project detection, and pipeline core so future features can be built through Larex itself.

Implement:
1. docs/requirements/REQ-0001-project-mission.md
2. knowledge/hallucination-policy.md
3. knowledge/approval-policy.md
4. knowledge/laravel-only-scope.md
5. knowledge/patch-policy.md
6. app/ArchEngine/TargetProject/TargetProject.php
7. app/ArchEngine/TargetProject/TargetProjectDetector.php
8. app/ArchEngine/DTO/TargetProjectFacts.php
9. app/ArchEngine/Pipeline/StageContract.php
10. app/ArchEngine/Pipeline/StageResult.php
11. app/ArchEngine/Pipeline/PipelineRunState.php
12. app/ArchEngine/Pipeline/PipelineRunner.php
13. app/Console/Commands/LarexInspectCommand.php
14. config/larex.php
15. tests/Feature/ArchEngine/TargetProjectDetectorTest.php
16. tests/Feature/ArchEngine/PipelineRunnerTest.php
17. tests/Feature/ArchEngine/LarexInspectCommandTest.php

Rules:
- Keep implementation minimal.
- CLI first, app later.
- Do not add external packages yet.
- Use typed PHP.
- Add tests.
- Run Pint and Pest if available.
- Report any command that cannot run.
```

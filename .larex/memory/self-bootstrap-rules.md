# Larex Self-Bootstrap Rules

## Bootstrap Principle

Larex must be able to build Larex from day one.

During self-bootstrapping:

- Larex repository is the selected target project.
- New features begin as requirement files.
- Changes should move through requirement, facts, plan, risk, patch, approval, implementation, and verification.

## Required Flow

```txt
docs/requirements/REQ-xxxx.md
-> RequirementBrief
-> TargetProjectFacts
-> ArchitecturePlan
-> RiskReport
-> PatchPlan
-> Approval
-> Implementation
-> Tests
-> ADR or LearningArtifact if accepted
```

## Current Bootstrap Exception

REQ-0001 creates durable memory only. It intentionally creates no PHP source code, CLI commands, dependencies, or runtime pipeline.

## Durable Memory Rule

Do not rely on `AGENTS.md` as permanent project memory. Persistent Larex project memory lives under `.larex/memory/`.

## Feature Intake Rule

Do not implement new Larex features directly from vague prompts unless the user explicitly requests an experimental prototype. If that happens, mark the work as experimental.

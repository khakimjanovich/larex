# Larex Self-Bootstrap Rules

## Bootstrap Principle

Larex must be able to build Larex from day one.

During self-bootstrapping:

- Larex repository is the selected target project.
- New features begin as GitHub milestones with labeled issues.
- Changes should move through requirement, facts, plan, risk, patch, approval, implementation, and verification.

## Required Flow

```txt
GitHub milestone (khakimjanovich/larex)
-> larex:milestone khakimjanovich larex {number}
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

## Planning Source

GitHub milestones are the only planning source. Local Markdown requirement files are no longer used or supported. Use the `larex:milestone` command to import milestones.

## Durable Memory Rule

Do not rely on `AGENTS.md` as permanent project memory. Persistent Larex project memory lives under `.larex/memory/`.

## Feature Intake Rule

Do not implement new Larex features directly from vague prompts unless the user explicitly requests an experimental prototype. If that happens, mark the work as experimental.

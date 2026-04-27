# Larex Architecture Rules

## Pipeline First

Larex owns the workflow. Do not design the system as direct prompt-to-code mutation.

Required flow:

```txt
Requirement
-> Select target project
-> Detect target project type
-> Normalize requirement
-> Inspect target repo
-> Ground in Laravel/PHP facts
-> Produce architecture plan
-> Audit risks
-> Produce patch plan
-> Human approval
-> Codex implementation
-> Verification
-> Artifact storage
```

## Stage Contracts

Every meaningful stage must have:

- typed input and output contract
- stage result status
- evidence references
- warnings and errors
- validation before the next stage consumes output
- tests when implemented

## Structured Artifacts Over Prose

Architecture, risk, patch, test, approval, verification, and learning outputs must be machine-checkable where practical.

## Agent Boundary

Agents are not free-floating collaborators. Every agent belongs to a pipeline stage and works through explicit contracts.

## Provider Boundary

OpenAI and Codex are first defaults, not permanent architectural assumptions. Larex should allow future reasoning providers through interfaces.

## Target Boundary

Never confuse Larex internal files with selected target project files. Detect and record the target project before planning or patching.

## MVP Constraint

Build the CLI flow before any cockpit UI. The cockpit may later visualize pipeline artifacts, but it must not create a separate workflow.

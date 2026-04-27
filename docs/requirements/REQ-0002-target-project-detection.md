# REQ-0002: Target Project Detection

## Status

Implemented.

## Goal

Detect the selected target project type and return structured target project facts without mutating files.

## Background

Larex must always distinguish the Larex repository from the selected target project. Before planning, auditing, patching, or implementing changes, Larex needs a verified `TargetProjectFacts` artifact.

## In Scope

- Create a target project detector.
- Create a target project value object.
- Create a target project facts DTO.
- Detect Laravel application projects.
- Detect Laravel packages.
- Detect Composer-based PHP packages.
- Return `unknown` when evidence is insufficient.
- Include evidence reference identifiers in the result.
- Add PHPUnit feature tests for detection behavior.

## Out of Scope

- CLI commands.
- Pipeline runner.
- Approval gates.
- Patch planning.
- Source mutation in detected target projects.
- Composer dependency changes.

## Acceptance Criteria

- Detects a Laravel application.
- Detects a Laravel package.
- Detects a Composer-based PHP package.
- Returns `unknown` when required evidence is missing.
- Produces the `target-project-facts-v1` contract.
- Does not mutate target project files.
- Has focused PHPUnit tests.

## Evidence References

- `README.md`: defines REQ-0002 scope and target project facts contract.
- `.larex/memory/project-profile.md`: defines Larex and target project boundary.
- `.larex/memory/architecture-rules.md`: requires target project detection before planning or patching.
- Laravel Boost docs search: confirms Laravel app structure and test conventions for this installed framework version.

## Unknowns

- Exact future artifact storage location for persisted facts.
- Whether later CLI output should be JSON-only or human-readable plus JSON.
- Whether package detection should later inspect installed `composer.lock` versions in addition to constraints.

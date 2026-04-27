# Current Task

## Requirement

REQ-0002: Target Project Detection.

## Goal

Detect whether a selected target path is a Laravel application, Laravel package, Composer-based PHP package, or unknown project.

## Scope

Created:

- `app/ArchEngine/TargetProject/TargetProject.php`
- `app/ArchEngine/TargetProject/TargetProjectDetector.php`
- `app/ArchEngine/DTO/TargetProjectFacts.php`
- `tests/Feature/ArchEngine/TargetProjectDetectorTest.php`
- `docs/requirements/REQ-0002-target-project-detection.md`

## Out of Scope

- CLI commands
- pipeline runner
- approval gates
- patch planning
- target project file mutation
- Composer dependency changes

## Status

Implemented.

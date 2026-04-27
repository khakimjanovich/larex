# Current Task

## Requirement

REQ-0005: Requirement Normalization.

## Goal

Turn a local Markdown requirement file into a structured `RequirementBrief`.

## Scope

Created:

- `app/ArchEngine/DTO/RequirementBrief.php`
- `app/ArchEngine/Tools/RequirementFileReader.php`
- `app/ArchEngine/Stages/NormalizeRequirementStage.php`
- `tests/Feature/ArchEngine/NormalizeRequirementStageTest.php`
- `docs/requirements/REQ-0005-requirement-normalization.md`

## Out of Scope

- top-level `larex` binary
- additional CLI commands beyond `larex:inspect`
- GitHub integration
- AI parsing
- approval gates
- patch planning
- target project file mutation
- Composer dependency changes

## Status

Implemented.

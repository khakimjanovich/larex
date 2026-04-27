# Current Task

## Requirement

REQ-0004: Larex CLI Skeleton.

## Goal

Expose the first usable Larex inspection surface from the terminal.

## Scope

Created:

- `app/Console/Commands/LarexInspectCommand.php`
- `config/larex.php`
- `tests/Feature/ArchEngine/LarexInspectCommandTest.php`
- `docs/requirements/REQ-0004-larex-cli-skeleton.md`

## Out of Scope

- top-level `larex` binary
- additional CLI commands beyond `larex:inspect`
- GitHub integration
- approval gates
- patch planning
- target project file mutation
- Composer dependency changes

## Status

Implemented.

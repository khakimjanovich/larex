# Current Task

## Requirement

Milestone 2: Requirement to Plan Pipeline (GitHub milestone #3).

## Goal

Turn a GitHub milestone into a structured architecture plan.

## Scope

Done:

- `app/ArchEngine/DTO/RequirementBrief.php`
- `app/ArchEngine/Stages/NormalizeGitHubMilestoneStage.php`
- `app/ArchEngine/Tools/GitHubClient.php`
- `app/ArchEngine/Tools/GitHubClientException.php`
- `app/Console/Commands/LarexMilestoneCommand.php`
- `tests/Feature/ArchEngine/NormalizeGitHubMilestoneStageTest.php`

Remaining open issues (https://github.com/khakimjanovich/larex/milestone/3):

- Integrate target inspection into pipeline as a stage (#8)
- Basic local artifact output — JSON file per pipeline run (#7)
- ArchitecturePlan DTO and stage (#13)
- Evidence reference structure (#14)
- larex:plan command produces an architecture plan from a requirement (#11)
- Pipeline run artifacts must not mix Larex internal files with target project files (#12)

## Out of Scope

- local Markdown requirement files (removed)
- risk auditing
- patch planning
- approval gates
- target project file mutation

## Status

In progress.

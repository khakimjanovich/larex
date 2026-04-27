# REQ-0006: GitHub Milestone Intake

## Status

Implemented.

## Goal

Read a GitHub milestone and its issues as read-only external planning input and produce a structured `RequirementBrief` using the same pipeline contract as local Markdown requirement files.

## Target Project

/Users/khakimjanovich/Documents/dev/arch-engine

## In Scope

- Create `GitHubClient` tool wrapping Laravel HTTP client.
- Create `NormalizeGitHubMilestoneStage` that maps a milestone to `requirement-brief-v1`.
- Map milestone title to `RequirementBrief.title`.
- Map milestone description to `RequirementBrief.goal`.
- Map issues labeled `larex:in-scope` to `inScope`.
- Map issues labeled `larex:out-of-scope` to `outOfScope`.
- Map issues labeled `larex:acceptance` to `acceptanceCriteria`.
- Map issues labeled `larex:constraint` to `constraints`.
- Map issues labeled `larex:question` to `openQuestions`.
- Accept `--project` option on CLI for `targetProjectPath`.
- Add `larex:milestone` Artisan command.
- Block when GitHub token is not configured.
- Block when milestone description is empty.
- Block when no issues are labeled `larex:acceptance`.
- Block on HTTP 401 or 404 responses.
- Add focused PHPUnit tests using `Http::fake()`.

## Out of Scope

- Writing to GitHub (issues, comments, labels, milestones).
- Mutating target project files.
- Architecture planning.
- Risk auditing.
- Patch planning.
- AI parsing or summarisation.
- Paginating beyond 100 issues per milestone.
- GitHub Apps authentication (token only).

## Acceptance Criteria

- `larex:milestone {owner} {repo} {milestone}` fetches the milestone and its issues via GitHub REST API v3.
- Produces `requirement-brief-v1` matching the same schema as `NormalizeRequirementStage`.
- Returns a blocked stage result when `LAREX_GITHUB_TOKEN` is not set.
- Returns a blocked stage result when the milestone description is empty.
- Returns a blocked stage result when no issues carry the `larex:acceptance` label.
- Returns a blocked stage result on HTTP 401 or 404.
- Does not write to any file or call any non-GET GitHub endpoint.
- Tests cover happy path, missing token, empty description, missing acceptance issues, and HTTP errors.

## Constraints

- No new Composer packages — use Laravel HTTP client.
- Read-only GitHub access.
- Token sourced from `LAREX_GITHUB_TOKEN` environment variable via `config('larex.github.token')`.
- Use `Http::fake()` in tests; no real GitHub calls in test suite.

## Open Questions

- Should issues without any `larex:` label fall through to `inScope` automatically?

# REQ-0005: Requirement Normalization

## Status

Implemented.

## Goal

Turn a local Markdown requirement file into a structured `RequirementBrief`.

## In Scope

- Create `RequirementBrief` DTO.
- Create deterministic requirement file reader/parser.
- Create `NormalizeRequirementStage`.
- Parse known Markdown sections.
- Return a structured `requirement-brief-v1` payload.
- Block when the file is missing.
- Block when required sections are missing.
- Add focused PHPUnit tests.

## Out of Scope

- AI parsing.
- GitHub integration.
- CLI plan command.
- Artifact persistence.
- Architecture planning.
- Risk auditing.
- Patch planning.
- Source mutation in target projects.

## Acceptance Criteria

- Reads a local Markdown requirement file.
- Extracts title, goal, target project path, scope, acceptance criteria, constraints, and open questions.
- Produces `requirement-brief-v1`.
- Returns a blocked stage result when the file is missing.
- Returns a blocked stage result when required content is missing.
- Does not mutate files.

## Constraints

- Deterministic parser only.
- No package installs.
- No network calls.
- Keep parsing format simple and explicit.

## Open Questions

- Should future requirements use front matter instead of Markdown headings?
- Should `target_project_path` default to current working directory if omitted?

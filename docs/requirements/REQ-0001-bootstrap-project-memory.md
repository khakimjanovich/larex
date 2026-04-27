# REQ-0001: Bootstrap Larex Project Memory

## Status

Implemented as documentation-only bootstrap memory.

## Goal

Create durable project memory for Larex from `README.md` and the agreed understanding of the project.

## Background

Larex is a Codex-powered, Laravel-controlled, schema-first, evidence-backed, approval-gated, self-bootstrapping architecture reasoning engine.

The project must not rely on `AGENTS.md` as permanent memory. Future Larex and Codex sessions need concise local memory files that preserve product identity, architecture rules, bootstrap rules, hallucination controls, development rules, testing rules, and project decisions.

## In Scope

- Create `.larex/memory/` files for durable project memory.
- Create `.larex/session/` files for current Codex session context.
- Record approved and rejected decisions.
- Mark unknowns clearly.
- Keep the memory concise and useful for future sessions.

## Out of Scope

- PHP source code.
- Composer dependency changes.
- Package installation.
- CLI command creation.
- Runtime pipeline implementation.
- Local cockpit or UI work.

## Acceptance Criteria

- `.larex/memory/project-profile.md` exists.
- `.larex/memory/architecture-rules.md` exists.
- `.larex/memory/self-bootstrap-rules.md` exists.
- `.larex/memory/hallucination-policy.md` exists.
- `.larex/memory/development-rules.md` exists.
- `.larex/memory/testing-rules.md` exists.
- `.larex/memory/approved-decisions.md` exists.
- `.larex/memory/rejected-decisions.md` exists.
- `.larex/session/codex-session-brief.md` exists.
- `.larex/session/current-task.md` exists.
- No PHP source code is created.
- `composer.json` is not modified.
- No packages are installed.
- No CLI command is created.

## Evidence References

- `README.md`: defines Larex identity, architecture rules, MVP direction, and bootstrap requirements.
- User clarification: product name is Larex; `larax` was a typo.
- Laravel Boost application info: current repository is a Laravel 13 application context.

## Unknowns

- Exact artifact storage path for future pipeline runs.
- Exact CLI registration mechanism for `larex`.
- Exact approval persistence mechanism.
- Exact reasoning provider abstraction.

## Recommended Next Requirement

REQ-0002 should implement target project detection.

Expected scope:

- detect Laravel application
- detect Laravel package
- detect Composer-based PHP package
- return `unknown` when evidence is insufficient
- produce structured target project facts
- avoid file mutation

# Larex Development Rules

## Current REQ-0001 Constraint

For REQ-0001, create only documentation and memory files. Do not create PHP source code, CLI commands, dependencies, or package changes.

## General Development Rules

- Follow existing Laravel project conventions.
- Keep changes small and scoped to the requirement.
- Prefer Laravel-native structure and Artisan generators when source code is introduced later.
- Do not modify `composer.json` or install packages without explicit approval.
- Do not add new base architecture folders without a requirement or approved plan.
- Do not mutate target project files before an approved patch plan.

## Codex Role

Codex is an implementation worker. Larex owns:

- task structure
- selected target project
- allowed write scope
- stage order
- evidence requirements
- risk gates
- approval gates
- verification gates

## Documentation Rule

Create documentation files only when explicitly requested or when required by the approved Larex workflow.

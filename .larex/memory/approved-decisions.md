# Approved Decisions

## Product Name

Decision: The product name is Larex.

Status: approved.

## First Surface

Decision: Build CLI-first.

Status: approved from README direction.

Context: The local app or cockpit is deferred and must consume the same pipeline artifacts as the CLI.

## Codex Boundary

Decision: Codex is a coding worker, not the owner of the whole product workflow.

Status: approved from README direction.

## Target Scope

Decision: MVP target projects are Laravel applications, Laravel packages, and Composer-based PHP packages.

Status: approved from README direction.

## REQ-0001 Scope

Decision: REQ-0001 creates durable project memory only.

Status: approved by user request.

Constraints:

- no PHP source code
- no Composer changes
- no package installation
- no CLI commands
- no reliance on `AGENTS.md` as permanent memory

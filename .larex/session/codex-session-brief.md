# Codex Session Brief

## Project

Larex is a Laravel-controlled architecture reasoning engine for Laravel applications, Laravel packages, and Composer-based PHP packages.

## Current State

- The repository currently appears to be a Laravel application skeleton.
- Durable project memory has been bootstrapped under `.larex/memory/`.
- REQ-0002 target project detection has been implemented as a pure PHP detector and DTO.
- REQ-0003 pipeline core has been implemented with runner, stage contract, stage result, and run state.
- REQ-0004 CLI inspect skeleton has been implemented as `php artisan larex:inspect`.

## Operating Mode

Future Codex sessions should read `.larex/memory/codex-bootstrap.md` first before proposing or implementing Larex features.

Do not rely on `AGENTS.md` as permanent product memory. It may provide session instructions, but Larex memory is under `.larex/memory/`.

## Immediate Next Step

Recommended next requirement: REQ-0005 GitHub Read Integration or Requirement Normalization.

GitHub Read Integration should define and implement:

- read-only milestone and issue intake.
- no GitHub writes.
- no direct code mutation from GitHub issues or comments.
- evidence refs such as `github.milestone.{number}` and `github.issue.{number}`.

## Unknowns

- Artifact run directory layout.
- Exact `larex` command registration strategy.
- Approval persistence mechanism.
- When to add a top-level `larex` binary wrapper.

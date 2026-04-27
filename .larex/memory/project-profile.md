# Larex Project Profile

## Identity

Larex is a Codex-powered, Laravel-controlled, schema-first, evidence-backed, approval-gated, self-bootstrapping architecture reasoning engine.

Larex is a developer product for Laravel applications, Laravel packages, and Composer-based PHP packages.

## Product Surfaces

- Primary MVP surface: CLI named `larex`.
- Possible later surface: local app or cockpit for viewing and controlling the same pipeline artifacts.

## Core Goal

A human selects a target Laravel/PHP project, writes a high-level requirement, and Larex converts it into validated artifacts:

- requirement brief
- target project facts
- architecture plan
- risk report
- patch plan
- approval decision
- verification result
- learning artifact

## Larex vs Target Project

- Larex repository: the product being built.
- Target project: the Laravel app, Laravel package, or Composer PHP package being inspected or changed.

During bootstrapping, the Larex repository is also the target project.

## Current Verified Context

- The repository is a Laravel application skeleton.
- Laravel Boost reports Laravel 13.6.0, PHP 8.4, SQLite, Laravel AI, Laravel MCP, Pint, Pest, PHPUnit, and Larastan installed.
- No Larex PHP source architecture has been created yet.

## Unknowns

- Final artifact storage format and run directory layout.
- Whether the first CLI command should be exposed as `larex` directly or via Artisan first.
- Exact approval persistence mechanism.
- Exact provider abstraction for reasoning models.

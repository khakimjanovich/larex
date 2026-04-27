# Larex

> AI-powered architecture reasoning engine for Laravel and PHP

[![PHP](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13%2B-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Tests](https://img.shields.io/badge/tests-40%20passing-brightgreen)](https://github.com/khakimjanovich/larex/actions)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)

Larex turns GitHub milestones into structured, evidence-backed, approval-gated code changes.

You describe what to build. Larex inspects your codebase, plans the architecture using AI, audits risks, produces a patch plan, and **waits for your approval before writing a single file**.

![Larex Demo](demo/demo.gif)

---

## The problem

Most AI coding tools go straight from prompt to code. That works for small scripts. It breaks on real codebases — wrong abstractions, missed conventions, no audit trail, no way to say no before the damage is done.

Larex puts a structured pipeline between the idea and the implementation.

---

## How it works

```
GitHub Milestone
  → Requirement Brief        (structured intake)
  → Target Project Facts     (codebase inspection)
  → Architecture Plan        (AI-backed, grounded in your code)
  → Risk Report              (what could go wrong)
  → Patch Plan               (exact files to change)
  → Human Approval    ←──── nothing is written without this
  → Implementation
  → Verification             (tests run, results stored)
  → Stored Learning
```

Every stage produces a typed artifact. Every artifact is evidence-backed. The human stays in the loop.

---

## Current capabilities

| Capability | Status |
|---|---|
| Detect Laravel apps, Laravel packages, PHP packages | ✅ Shipped |
| Import GitHub milestones as requirement briefs | ✅ Shipped |
| Read and search target project source files | ✅ Shipped |
| Produce AI-backed architecture plans (Anthropic) | ✅ Shipped |
| Store pipeline run artifacts | ✅ Shipped |
| Enforce approval gate before file writes | ✅ Shipped |
| Apply approved patch plans to target projects | ✅ Shipped |
| Risk audit stage | 🔄 In progress |
| `larex:plan` end-to-end command | 🔄 In progress |
| `larex:approve` and `larex:patch` commands | 📋 Planned |
| Verification stage | 📋 Planned |
| `larex` standalone binary | 📋 Planned |

---

## Usage today

```bash
# Inspect a target project — detect type, extract facts
php artisan larex:inspect --project=/path/to/my-laravel-app

# Import a GitHub milestone as a structured requirement brief
php artisan larex:milestone myorg my-repo 3 --project=/path/to/my-laravel-app
```

The milestone command uses a label scheme on your GitHub issues:

| Label | Maps to |
|---|---|
| `larex:in-scope` | In scope items |
| `larex:out-of-scope` | Out of scope items |
| `larex:acceptance` | Acceptance criteria (required) |
| `larex:constraint` | Constraints |
| `larex:question` | Open questions |

Unlabeled issues fall through to in-scope automatically.

---

## Requirements

- PHP 8.4+
- Laravel 13+
- Anthropic API key — set `LAREX_ANTHROPIC_KEY` or use the standard `ANTHROPIC_API_KEY`
- GitHub token for milestone intake — set `LAREX_GITHUB_TOKEN`

---

## Self-bootstrapping

Larex is built using its own pipeline. Every feature starts as a GitHub milestone on this repo. The engine inspects itself, plans its own next changes, and applies them through its own approval gate.

[Track progress on GitHub Milestones →](https://github.com/khakimjanovich/larex/milestones)

---

## Roadmap

The MVP delivers a five-command workflow:

```bash
larex:milestone myorg my-repo 3     # import requirement
larex:plan RUN-0001                  # produce architecture plan
larex:audit RUN-0001                 # audit risks
larex:approve RUN-0001               # human approval
larex:patch RUN-0001                 # apply to target project
```

Every command is safe to run in CI. Nothing mutates your codebase until `larex:approve`.

Milestones 0–2 are complete. Milestones 3–4 ship the MVP.

---

## Architecture

Larex is a standard Laravel application. The engine lives in `app/ArchEngine/`:

```
app/ArchEngine/
  DTO/           — typed value objects for every artifact schema
  Enums/         — MutationType and friends
  Pipeline/      — PipelineRunner, StageContract, StageResult
  Stages/        — one class per pipeline stage
  Stores/        — RunStore (artifact persistence)
  TargetProject/ — project type detection
  Tools/         — CodebaseReader, GitHubClient
  Workers/       — PatchWorker (applies approved patch plans)

app/Ai/Agents/   — Laravel AI SDK agents (ArchitecturePlannerAgent)

app/Console/Commands/
  larex:inspect
  larex:milestone
```

All stage results carry a status (`succeeded` / `failed` / `blocked`), a typed payload, evidence refs, warnings, and errors. Non-recoverable failures stop the pipeline. Recoverable failures continue.

---

## Contributing

Issues and PRs are welcome. New features follow the same pipeline that Larex uses on other projects — open a milestone, label your issues, and the engine will plan the implementation.

---

## License

MIT

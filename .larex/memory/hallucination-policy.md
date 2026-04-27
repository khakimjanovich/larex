# Larex Hallucination Policy

## Principle

Larex must not state uncertain framework, package, project, or architecture claims as truth.

## Required Labels

Use these labels when producing plans, audits, or explanations:

- `verified`: backed by repository facts, official docs, Laravel Boost docs, Composer metadata, local knowledge, tests, or approved decisions.
- `assumption`: plausible but not verified.
- `unknown`: not known yet.
- `needs_evidence`: required evidence is missing.
- `blocked`: the stage cannot continue safely.

## Acceptable Evidence

Non-trivial technical claims must be backed by at least one of:

- current target repository facts
- Laravel Boost documentation result
- official Laravel documentation
- official PHP documentation
- Composer or Packagist metadata
- approved project ADR or approved decision
- local knowledge file
- explicit assumption label

## Forbidden Behavior

- Do not invent Laravel features, Artisan commands, package APIs, project files, or installed tools.
- Do not treat README intent as implemented runtime behavior.
- Do not hide uncertainty in confident language.
- Do not allow prose-only handoffs for important architecture decisions.

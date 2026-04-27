# Larex Testing Rules

## Principle

Tests are required before trusting code changes.

## Current REQ-0001

REQ-0001 is documentation-only and creates no executable code. No automated test is required for this requirement.

## Future Code Requirements

When PHP source code is introduced:

- Add or update relevant tests.
- Prefer focused tests for the changed behavior.
- Use PHPUnit classes for tests in this project unless the project rule changes.
- Run the smallest relevant test set before finalizing.
- Run Pint on modified PHP files.
- Do not delete tests without explicit approval.

## Verification Outputs

Verification results should record:

- command run
- status
- relevant output summary
- failures or skipped checks
- whether the result blocks approval

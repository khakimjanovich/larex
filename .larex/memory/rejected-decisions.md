# Rejected Decisions

## Prompt Directly Edits Code

Decision: rejected.

Reason: Larex must own the staged workflow, evidence requirements, risk gates, approval gates, and verification.

## Cockpit Before CLI

Decision: rejected for MVP.

Reason: The README defines CLI as the first useful surface. A cockpit can come later as a visualizer/controller for the same artifacts.

## General-Purpose No-Code Builder

Decision: rejected.

Reason: Larex scope is Laravel/PHP architecture assistance, not a general-purpose app builder.

## Non-PHP Project Support

Decision: rejected for MVP.

Reason: Target scope is Laravel applications, Laravel packages, and Composer-based PHP packages.

## Unapproved Mutation

Decision: rejected.

Reason: Meaningful file mutation must wait for an approved patch plan.

## README Intent Equals Implementation

Decision: rejected.

Reason: README describes intended architecture. Runtime behavior must be verified in source, tests, or generated artifacts.

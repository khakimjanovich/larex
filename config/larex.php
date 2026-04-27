<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Larex CLI
    |--------------------------------------------------------------------------
    |
    | The first CLI surface is intentionally small. It exposes project
    | inspection without mutating the selected target project.
    |
    */

    'cli' => [
        'inspect_command' => 'larex:inspect',
    ],

    /*
    |--------------------------------------------------------------------------
    | GitHub Integration
    |--------------------------------------------------------------------------
    |
    | Read-only GitHub access for milestone intake. The token must have at
    | least the `repo` (private) or no scope (public) to read milestones
    | and issues. Set LAREX_GITHUB_TOKEN in your environment.
    |
    */

    'github' => [
        'token' => env('LAREX_GITHUB_TOKEN'),
    ],

];

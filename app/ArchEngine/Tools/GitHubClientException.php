<?php

namespace App\ArchEngine\Tools;

use RuntimeException;

final class GitHubClientException extends RuntimeException
{
    public static function missingToken(): self
    {
        return new self('LAREX_GITHUB_TOKEN is not configured.');
    }

    public static function unauthorized(): self
    {
        return new self('GitHub API returned 401. Check that LAREX_GITHUB_TOKEN is valid.');
    }

    public static function notFound(string $detail): self
    {
        return new self($detail);
    }

    public static function httpError(string $detail): self
    {
        return new self('GitHub API error: '.$detail);
    }
}

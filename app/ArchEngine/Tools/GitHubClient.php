<?php

namespace App\ArchEngine\Tools;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final class GitHubClient
{
    private const ApiBase = 'https://api.github.com';

    public function __construct(
        private readonly string $owner,
        private readonly string $repo,
        private readonly string $token,
    ) {}

    /**
     * @return array{number: int, title: string, description: string|null, html_url: string}
     *
     * @throws GitHubClientException
     */
    public function getMilestone(int $number): array
    {
        $response = Http::withToken($this->token)
            ->accept('application/vnd.github+json')
            ->timeout(15)
            ->get(self::ApiBase."/repos/{$this->owner}/{$this->repo}/milestones/{$number}");

        if ($response->status() === 401) {
            throw GitHubClientException::unauthorized();
        }

        if ($response->status() === 404) {
            throw GitHubClientException::notFound("Milestone {$number} not found in {$this->owner}/{$this->repo}.");
        }

        try {
            $response->throw();
        } catch (RequestException $e) {
            throw GitHubClientException::httpError($e->getMessage());
        }

        return $response->json();
    }

    /**
     * @return list<array{number: int, title: string, labels: list<array{name: string}>}>
     *
     * @throws GitHubClientException
     */
    public function getMilestoneIssues(int $milestoneNumber): array
    {
        $response = Http::withToken($this->token)
            ->accept('application/vnd.github+json')
            ->timeout(15)
            ->get(self::ApiBase."/repos/{$this->owner}/{$this->repo}/issues", [
                'milestone' => $milestoneNumber,
                'state' => 'all',
                'per_page' => 100,
            ]);

        if ($response->status() === 401) {
            throw GitHubClientException::unauthorized();
        }

        if ($response->status() === 404) {
            throw GitHubClientException::notFound("Repository {$this->owner}/{$this->repo} not found.");
        }

        try {
            $response->throw();
        } catch (RequestException $e) {
            throw GitHubClientException::httpError($e->getMessage());
        }

        return $response->json();
    }
}

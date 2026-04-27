<?php

namespace App\Console\Commands;

use App\ArchEngine\Stages\NormalizeGitHubMilestoneStage;
use App\ArchEngine\Tools\GitHubClient;
use App\ArchEngine\Tools\GitHubClientException;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('larex:milestone {owner} {repo} {milestone : GitHub milestone number} {--project= : Absolute path to target project}')]
#[Description('Import a GitHub milestone as a structured requirement brief')]
class LarexMilestoneCommand extends Command
{
    public function handle(): int
    {
        $token = config('larex.github.token');

        if (! is_string($token) || trim($token) === '') {
            $this->line((string) json_encode([
                'status' => 'blocked',
                'errors' => [GitHubClientException::missingToken()->getMessage()],
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

            return 2;
        }

        $owner = (string) $this->argument('owner');
        $repo = (string) $this->argument('repo');
        $milestoneNumber = (int) $this->argument('milestone');
        $projectPath = $this->option('project');

        if (! is_string($projectPath) || $projectPath === '') {
            $projectPath = null;
        }

        $stage = new NormalizeGitHubMilestoneStage(
            owner: $owner,
            repo: $repo,
            milestoneNumber: $milestoneNumber,
            targetProjectPath: $projectPath,
            client: new GitHubClient($owner, $repo, $token),
        );

        $result = $stage->handle();

        $this->line((string) json_encode([
            'status' => $result->status,
            'payload' => $result->payload,
            'evidence_refs' => $result->evidenceRefs,
            'warnings' => $result->warnings,
            'errors' => $result->errors,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return $result->isSuccessful() ? self::SUCCESS : 2;
    }
}

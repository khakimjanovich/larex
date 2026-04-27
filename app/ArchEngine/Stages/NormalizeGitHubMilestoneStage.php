<?php

namespace App\ArchEngine\Stages;

use App\ArchEngine\DTO\RequirementBrief;
use App\ArchEngine\Pipeline\StageResult;
use App\ArchEngine\Tools\GitHubClient;
use App\ArchEngine\Tools\GitHubClientException;

final class NormalizeGitHubMilestoneStage
{
    private const SchemaVersion = 'requirement-brief-v1';

    public function __construct(
        private readonly string $owner,
        private readonly string $repo,
        private readonly int $milestoneNumber,
        private readonly ?string $targetProjectPath,
        private readonly GitHubClient $client,
    ) {}

    public function handle(): StageResult
    {
        try {
            $milestone = $this->client->getMilestone($this->milestoneNumber);
            $issues = $this->client->getMilestoneIssues($this->milestoneNumber);
        } catch (GitHubClientException $e) {
            return StageResult::blocked(
                evidenceRefs: ['github.api_error'],
                errors: [$e->getMessage()],
            );
        }

        $goal = trim((string) ($milestone['description'] ?? ''));

        if ($goal === '') {
            return StageResult::blocked(
                evidenceRefs: ['github.milestone'],
                errors: ['Milestone description is empty. Add a description to use it as the requirement goal.'],
            );
        }

        $categorized = $this->categorizeIssues($issues);

        if ($categorized['acceptance'] === []) {
            return StageResult::blocked(
                evidenceRefs: ['github.milestone_issues'],
                errors: ['No issues are labeled larex:acceptance. Label at least one issue larex:acceptance to define acceptance criteria.'],
            );
        }

        $brief = new RequirementBrief(
            schemaVersion: self::SchemaVersion,
            title: trim((string) ($milestone['title'] ?? 'Untitled Milestone')),
            goal: $goal,
            targetProjectPath: $this->targetProjectPath,
            inScope: $categorized['in-scope'],
            outOfScope: $categorized['out-of-scope'],
            acceptanceCriteria: $categorized['acceptance'],
            constraints: $categorized['constraint'],
            openQuestions: $categorized['question'],
        );

        return StageResult::succeeded(
            payload: $brief->toArray(),
            evidenceRefs: [
                'github.milestone:'.$this->owner.'/'.$this->repo.'#'.$this->milestoneNumber,
            ],
        );
    }

    /**
     * @param  list<array{title: string, labels: list<array{name: string}>}>  $issues
     * @return array{in-scope: list<string>, out-of-scope: list<string>, acceptance: list<string>, constraint: list<string>, question: list<string>}
     */
    private function categorizeIssues(array $issues): array
    {
        $buckets = [
            'in-scope' => [],
            'out-of-scope' => [],
            'acceptance' => [],
            'constraint' => [],
            'question' => [],
        ];

        foreach ($issues as $issue) {
            $title = trim((string) ($issue['title'] ?? ''));

            if ($title === '') {
                continue;
            }

            $labelNames = array_map(
                fn (array $label): string => strtolower((string) ($label['name'] ?? '')),
                $issue['labels'] ?? [],
            );

            $matched = false;

            foreach (['in-scope', 'out-of-scope', 'acceptance', 'constraint', 'question'] as $bucket) {
                if (in_array('larex:'.$bucket, $labelNames, true)) {
                    $buckets[$bucket][] = $title;
                    $matched = true;
                }
            }

            if (! $matched) {
                $buckets['in-scope'][] = $title;
            }
        }

        return $buckets;
    }
}

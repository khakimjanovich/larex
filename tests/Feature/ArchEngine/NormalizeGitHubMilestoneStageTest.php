<?php

namespace Tests\Feature\ArchEngine;

use App\ArchEngine\Stages\NormalizeGitHubMilestoneStage;
use App\ArchEngine\Tools\GitHubClient;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NormalizeGitHubMilestoneStageTest extends TestCase
{
    #[Test]
    public function it_produces_a_requirement_brief_from_a_milestone(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/1' => Http::response([
                'number' => 1,
                'title' => 'MVP: User Authentication',
                'description' => 'Implement secure user login and registration flow.',
                'html_url' => 'https://github.com/acme/my-app/milestone/1',
            ]),
            'api.github.com/repos/acme/my-app/issues*' => Http::response([
                ['title' => 'Add login form', 'labels' => [['name' => 'larex:in-scope']]],
                ['title' => 'Add registration form', 'labels' => [['name' => 'larex:in-scope']]],
                ['title' => 'OAuth login', 'labels' => [['name' => 'larex:out-of-scope']]],
                ['title' => 'User can log in with email and password', 'labels' => [['name' => 'larex:acceptance']]],
                ['title' => 'User can register with unique email', 'labels' => [['name' => 'larex:acceptance']]],
                ['title' => 'Passwords must be hashed with bcrypt', 'labels' => [['name' => 'larex:constraint']]],
                ['title' => 'Should we support SSO in future?', 'labels' => [['name' => 'larex:question']]],
            ]),
        ]);

        $result = $this->makeStage(milestoneNumber: 1, targetProjectPath: '/tmp/my-app')->handle();

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('requirement-brief-v1', $result->payload['schema_version']);
        $this->assertSame('MVP: User Authentication', $result->payload['title']);
        $this->assertSame('Implement secure user login and registration flow.', $result->payload['goal']);
        $this->assertSame('/tmp/my-app', $result->payload['target_project_path']);
        $this->assertSame(['Add login form', 'Add registration form'], $result->payload['in_scope']);
        $this->assertSame(['OAuth login'], $result->payload['out_of_scope']);
        $this->assertSame([
            'User can log in with email and password',
            'User can register with unique email',
        ], $result->payload['acceptance_criteria']);
        $this->assertSame(['Passwords must be hashed with bcrypt'], $result->payload['constraints']);
        $this->assertSame(['Should we support SSO in future?'], $result->payload['open_questions']);
        $this->assertContains('github.milestone:acme/my-app#1', $result->evidenceRefs);
    }

    #[Test]
    public function it_places_unlabeled_issues_into_in_scope(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/2' => Http::response([
                'number' => 2,
                'title' => 'Billing',
                'description' => 'Add subscription billing.',
            ]),
            'api.github.com/repos/acme/my-app/issues*' => Http::response([
                ['title' => 'Add Stripe integration', 'labels' => []],
                ['title' => 'Subscription can be cancelled', 'labels' => [['name' => 'larex:acceptance']]],
            ]),
        ]);

        $result = $this->makeStage(milestoneNumber: 2)->handle();

        $this->assertTrue($result->isSuccessful());
        $this->assertContains('Add Stripe integration', $result->payload['in_scope']);
    }

    #[Test]
    public function it_blocks_when_milestone_description_is_empty(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/3' => Http::response([
                'number' => 3,
                'title' => 'No description milestone',
                'description' => '',
            ]),
            'api.github.com/repos/acme/my-app/issues*' => Http::response([]),
        ]);

        $result = $this->makeStage(milestoneNumber: 3)->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertContains('github.milestone', $result->evidenceRefs);
        $this->assertStringContainsString('description is empty', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_when_no_issues_are_labeled_larex_acceptance(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/4' => Http::response([
                'number' => 4,
                'title' => 'Some Feature',
                'description' => 'Do some feature.',
            ]),
            'api.github.com/repos/acme/my-app/issues*' => Http::response([
                ['title' => 'Task one', 'labels' => []],
                ['title' => 'Task two', 'labels' => [['name' => 'larex:in-scope']]],
            ]),
        ]);

        $result = $this->makeStage(milestoneNumber: 4)->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertContains('github.milestone_issues', $result->evidenceRefs);
        $this->assertStringContainsString('larex:acceptance', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_on_http_401(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/5' => Http::response([], 401),
        ]);

        $result = $this->makeStage(milestoneNumber: 5)->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertContains('github.api_error', $result->evidenceRefs);
        $this->assertStringContainsString('401', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_on_http_404(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/99' => Http::response([], 404),
        ]);

        $result = $this->makeStage(milestoneNumber: 99)->handle();

        $this->assertSame('blocked', $result->status);
        $this->assertContains('github.api_error', $result->evidenceRefs);
        $this->assertStringContainsString('not found', strtolower($result->errors[0]));
    }

    #[Test]
    public function it_uses_null_target_project_path_when_not_provided(): void
    {
        Http::fake([
            'api.github.com/repos/acme/my-app/milestones/6' => Http::response([
                'number' => 6,
                'title' => 'Some Milestone',
                'description' => 'Do something.',
            ]),
            'api.github.com/repos/acme/my-app/issues*' => Http::response([
                ['title' => 'Acceptance item', 'labels' => [['name' => 'larex:acceptance']]],
            ]),
        ]);

        $result = $this->makeStage(milestoneNumber: 6, targetProjectPath: null)->handle();

        $this->assertTrue($result->isSuccessful());
        $this->assertNull($result->payload['target_project_path']);
    }

    private function makeStage(int $milestoneNumber, ?string $targetProjectPath = null): NormalizeGitHubMilestoneStage
    {
        return new NormalizeGitHubMilestoneStage(
            owner: 'acme',
            repo: 'my-app',
            milestoneNumber: $milestoneNumber,
            targetProjectPath: $targetProjectPath,
            client: new GitHubClient('acme', 'my-app', 'fake-token'),
        );
    }
}

<?php

namespace Tests\Feature\ArchEngine;

use App\ArchEngine\DTO\ApprovalDecision;
use App\ArchEngine\DTO\Mutation;
use App\ArchEngine\DTO\PatchPlan;
use App\ArchEngine\Stores\RunStore;
use App\ArchEngine\Workers\PatchWorker;
use PHPUnit\Framework\Attributes\Test;
use Random\RandomException;
use Tests\TestCase;

class PatchWorkerTest extends TestCase
{
    private string $runsDir;

    private string $targetDir;

    private RunStore $store;

    /**
     * @throws RandomException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $base = sys_get_temp_dir().'/larex-patch-'.bin2hex(random_bytes(8));
        $this->runsDir = $base.'/runs';
        $this->targetDir = $base.'/target';

        mkdir($this->runsDir, 0777, true);
        mkdir($this->targetDir, 0777, true);

        $this->store = new RunStore($this->runsDir);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory(dirname($this->runsDir));
    }

    #[Test]
    public function it_creates_a_new_file_in_the_target(): void
    {
        $runId = $this->seedRun([Mutation::createFile('src/Hello.php', '<?php echo "hello";')]);

        $result = $this->worker()->apply($runId);

        $this->assertTrue($result->success);
        $this->assertFileExists($this->targetDir.'/src/Hello.php');
        $this->assertStringContainsString('hello', file_get_contents($this->targetDir.'/src/Hello.php'));
        $this->assertContains('create_file: src/Hello.php', $result->appliedMutations);
    }

    #[Test]
    public function it_edits_an_existing_file_in_the_target(): void
    {
        file_put_contents($this->targetDir.'/Config.php', '<?php return ["debug" => false];');

        $runId = $this->seedRun([Mutation::editFile('Config.php', '"debug" => false', '"debug" => true')]);

        $result = $this->worker()->apply($runId);

        $this->assertTrue($result->success);
        $this->assertStringContainsString('"debug" => true', file_get_contents($this->targetDir.'/Config.php'));
        $this->assertContains('edit_file: Config.php', $result->appliedMutations);
    }

    #[Test]
    public function it_fails_when_old_string_not_found_in_file(): void
    {
        file_put_contents($this->targetDir.'/Config.php', '<?php return [];');

        $runId = $this->seedRun([Mutation::editFile('Config.php', '"debug" => false', '"debug" => true')]);

        $result = $this->worker()->apply($runId);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('old_string not found', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_when_approval_decision_is_missing(): void
    {
        $runId = $this->store->createRun();
        $this->store->saveArtifact($runId, 'patch_plan', (new PatchPlan('patch-plan-v1', $runId, []))->toArray());

        $result = $this->worker()->apply($runId);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('approval decision', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_when_patch_plan_is_missing(): void
    {
        $runId = $this->store->createRun();
        $approval = new ApprovalDecision('approval-decision-v1', $runId, now()->toIso8601String(), 'human');
        $this->store->saveArtifact($runId, 'approval_decision', $approval->toArray());

        $result = $this->worker()->apply($runId);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('patch plan', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_unsafe_commands(): void
    {
        $runId = $this->seedRun([Mutation::runCommand('rm -rf /')]);

        $result = $this->worker()->apply($runId);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('not in safe list', $result->errors[0]);
    }

    #[Test]
    public function it_blocks_path_traversal(): void
    {
        $runId = $this->seedRun([Mutation::createFile('../outside.php', '<?php')]);

        $result = $this->worker()->apply($runId);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('traversal', $result->errors[0]);
    }

    #[Test]
    public function it_saves_a_verification_artifact_on_success(): void
    {
        $runId = $this->seedRun([Mutation::createFile('Marker.php', '<?php')]);

        $this->worker()->apply($runId);

        $artifact = $this->store->loadArtifact($runId, 'verification');

        $this->assertNotNull($artifact);
        $this->assertSame('verification-result-v1', $artifact['schema_version']);
        $this->assertSame($runId, $artifact['run_id']);
    }

    #[Test]
    public function it_applies_multiple_mutations_in_order(): void
    {
        $runId = $this->seedRun([
            Mutation::createFile('A.php', '<?php // A'),
            Mutation::createFile('B.php', '<?php // B'),
        ]);

        $result = $this->worker()->apply($runId);

        $this->assertTrue($result->success);
        $this->assertCount(2, $result->appliedMutations);
        $this->assertFileExists($this->targetDir.'/A.php');
        $this->assertFileExists($this->targetDir.'/B.php');
    }

    /** @param list<Mutation> $mutations */
    private function seedRun(array $mutations): string
    {
        $runId = $this->store->createRun();

        $approval = new ApprovalDecision('approval-decision-v1', $runId, now()->toIso8601String(), 'human');
        $this->store->saveArtifact($runId, 'approval_decision', $approval->toArray());

        $plan = new PatchPlan('patch-plan-v1', $runId, $mutations);
        $this->store->saveArtifact($runId, 'patch_plan', $plan->toArray());

        return $runId;
    }

    private function worker(): PatchWorker
    {
        return new PatchWorker(store: $this->store, targetPath: $this->targetDir);
    }

    private function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        foreach (scandir($path) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $full = $path.'/'.$item;
            is_dir($full) ? $this->removeDirectory($full) : unlink($full);
        }

        rmdir($path);
    }
}

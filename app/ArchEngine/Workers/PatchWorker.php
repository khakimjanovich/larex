<?php

namespace App\ArchEngine\Workers;

use App\ArchEngine\DTO\ApprovalDecision;
use App\ArchEngine\DTO\Mutation;
use App\ArchEngine\DTO\PatchPlan;
use App\ArchEngine\Enums\MutationType;
use App\ArchEngine\Stores\RunStore;

final class PatchWorker
{
    private const SafeCommandPrefixes = [
        'php artisan make:',
        'php artisan migrate',
        'vendor/bin/pint',
        'composer dump-autoload',
    ];

    public function __construct(
        private readonly RunStore $store,
        private readonly string $targetPath,
    ) {}

    public function apply(string $runId): PatchWorkerResult
    {
        $approvalData = $this->store->loadArtifact($runId, 'approval_decision');

        if ($approvalData === null) {
            return PatchWorkerResult::blocked('No approval decision found for run '.$runId.'. Run larex:approve first.');
        }

        $approval = ApprovalDecision::fromArray($approvalData);

        if ($approval->runId !== $runId) {
            return PatchWorkerResult::blocked('Approval decision run ID mismatch: expected '.$runId.', got '.$approval->runId.'.');
        }

        $planData = $this->store->loadArtifact($runId, 'patch_plan');

        if ($planData === null) {
            return PatchWorkerResult::blocked('No patch plan found for run '.$runId.'.');
        }

        if (! is_dir($this->targetPath)) {
            return PatchWorkerResult::blocked('Target path does not exist: '.$this->targetPath.'.');
        }

        $plan = PatchPlan::fromArray($planData);
        $applied = [];

        foreach ($plan->mutations as $mutation) {
            $result = $this->applyMutation($mutation);

            if (! $result['success']) {
                return PatchWorkerResult::failed(appliedMutations: $applied, error: $result['error']);
            }

            $applied[] = $result['description'];
        }

        $this->store->saveArtifact($runId, 'verification', [
            'schema_version' => 'verification-result-v1',
            'run_id' => $runId,
            'applied_mutations' => $applied,
            'verified_at' => now()->toIso8601String(),
        ]);

        return PatchWorkerResult::succeeded($applied);
    }

    /** @return array{success: bool, description: string, error: string} */
    private function applyMutation(Mutation $mutation): array
    {
        return match ($mutation->type) {
            MutationType::CreateFile => $this->createFile($mutation),
            MutationType::EditFile => $this->editFile($mutation),
            MutationType::RunCommand => $this->runCommand($mutation),
        };
    }

    /** @return array{success: bool, description: string, error: string} */
    private function createFile(Mutation $mutation): array
    {
        $path = (string) $mutation->path;

        try {
            $resolved = $this->resolveAndValidatePath($path);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'description' => '', 'error' => $e->getMessage()];
        }

        $dir = dirname($resolved);

        if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir)) {
            return ['success' => false, 'description' => '', 'error' => 'Failed to create directory: '.$dir.'.'];
        }

        file_put_contents($resolved, (string) $mutation->content);

        return ['success' => true, 'description' => 'create_file: '.$path, 'error' => ''];
    }

    /** @return array{success: bool, description: string, error: string} */
    private function editFile(Mutation $mutation): array
    {
        $path = (string) $mutation->path;

        try {
            $resolved = $this->resolveAndValidatePath($path);
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'description' => '', 'error' => $e->getMessage()];
        }

        if (! is_file($resolved)) {
            return ['success' => false, 'description' => '', 'error' => 'File does not exist: '.$path.'.'];
        }

        $contents = (string) file_get_contents($resolved);
        $oldString = (string) $mutation->oldString;

        if (! str_contains($contents, $oldString)) {
            return ['success' => false, 'description' => '', 'error' => 'old_string not found in '.$path.'.'];
        }

        file_put_contents($resolved, str_replace($oldString, (string) $mutation->newString, $contents));

        return ['success' => true, 'description' => 'edit_file: '.$path, 'error' => ''];
    }

    /** @return array{success: bool, description: string, error: string} */
    private function runCommand(Mutation $mutation): array
    {
        $command = trim((string) $mutation->command);

        if (! $this->isCommandSafe($command)) {
            return ['success' => false, 'description' => '', 'error' => 'Command not in safe list: '.$command.'.'];
        }

        $output = [];
        $exitCode = 0;
        exec('cd '.escapeshellarg($this->targetPath).' && '.$command.' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            return ['success' => false, 'description' => '', 'error' => 'Command failed ('.$exitCode.'): '.implode("\n", $output)];
        }

        return ['success' => true, 'description' => 'run_command: '.$command, 'error' => ''];
    }

    private function resolveAndValidatePath(string $path): string
    {
        $target = rtrim($this->targetPath, '/');
        $resolved = str_starts_with($path, '/') ? $path : $target.'/'.$path;
        $resolved = (string) preg_replace('#/+#', '/', $resolved);

        if (str_contains($resolved, '/../') || str_ends_with($resolved, '/..')) {
            throw new \InvalidArgumentException('Path traversal detected: '.$path.'.');
        }

        if (! str_starts_with($resolved, $target.'/') && $resolved !== $target) {
            throw new \InvalidArgumentException('Path is outside target project: '.$path.'.');
        }

        return $resolved;
    }

    private function isCommandSafe(string $command): bool
    {
        foreach (self::SafeCommandPrefixes as $prefix) {
            if (str_starts_with($command, $prefix)) {
                return true;
            }
        }

        return false;
    }
}

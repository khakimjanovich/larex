<?php

namespace App\Console\Commands;

use App\ArchEngine\TargetProject\TargetProject;
use App\ArchEngine\TargetProject\TargetProjectDetector;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('larex:inspect {--project= : Absolute or relative path to the target project}')]
#[Description('Inspect a Laravel or Composer PHP target project without mutating files')]
class LarexInspectCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(TargetProjectDetector $detector): int
    {
        $projectPath = $this->option('project') ?: getcwd();

        if (! is_string($projectPath) || $projectPath === '') {
            $projectPath = getcwd();
        }

        $facts = $detector->detect($projectPath);
        $status = $facts->projectType === TargetProject::Unknown ? 'blocked' : 'succeeded';

        $this->line((string) json_encode([
            'status' => $status,
            'facts' => $facts->toArray(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));

        return $status === 'blocked' ? 2 : self::SUCCESS;
    }
}

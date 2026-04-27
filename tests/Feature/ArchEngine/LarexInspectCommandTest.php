<?php

namespace Tests\Feature\ArchEngine;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Random\RandomException;
use Tests\TestCase;

class LarexInspectCommandTest extends TestCase
{
    #[Test]
    public function it_inspects_the_current_working_directory_by_default(): void
    {
        $exitCode = Artisan::call('larex:inspect');
        $payload = $this->commandPayload();

        $this->assertSame(0, $exitCode);
        $this->assertSame('succeeded', $payload['status']);
        $this->assertSame('target-project-facts-v1', $payload['facts']['schema_version']);
        $this->assertSame('laravel_app', $payload['facts']['project_type']);
        $this->assertSame('laravel/laravel', $payload['facts']['composer_name']);
    }

    #[Test]
    public function it_inspects_an_explicit_project_path(): void
    {
        $projectPath = $this->makeProject([
            'composer.json' => json_encode([
                'name' => 'acme/plain-package',
                'type' => 'library',
                'require' => [
                    'php' => '^8.4',
                ],
                'require-dev' => [
                    'phpunit/phpunit' => '^12.0',
                ],
            ], JSON_THROW_ON_ERROR),
        ]);

        $exitCode = Artisan::call('larex:inspect', ['--project' => $projectPath]);
        $payload = $this->commandPayload();

        $this->assertSame(0, $exitCode);
        $this->assertSame('succeeded', $payload['status']);
        $this->assertSame('php_package', $payload['facts']['project_type']);
        $this->assertSame('acme/plain-package', $payload['facts']['composer_name']);
    }

    #[Test]
    public function it_returns_blocked_output_for_unknown_targets(): void
    {
        $projectPath = $this->makeProject([
            'README.md' => '# Missing composer evidence',
        ]);

        $exitCode = Artisan::call('larex:inspect', ['--project' => $projectPath]);
        $payload = $this->commandPayload();

        $this->assertSame(2, $exitCode);
        $this->assertSame('blocked', $payload['status']);
        $this->assertSame('unknown', $payload['facts']['project_type']);
        $this->assertContains('target.missing_composer_json', $payload['facts']['evidence_refs']);
    }

    /**
     * @return array{
     *     status: string,
     *     facts: array{
     *         schema_version: string,
     *         project_type: string,
     *         composer_name: string|null,
     *         evidence_refs: list<string>
     *     }
     * }
     */
    private function commandPayload(): array
    {
        $payload = json_decode(Artisan::output(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertIsArray($payload);

        return $payload;
    }

    /**
     * @param  array<string, string>  $files
     *
     * @throws RandomException
     */
    private function makeProject(array $files): string
    {
        $projectPath = sys_get_temp_dir().'/larex-cli-target-'.bin2hex(random_bytes(8));

        mkdir($projectPath, 0777, true);

        foreach ($files as $relativePath => $contents) {
            $absolutePath = $projectPath.'/'.$relativePath;
            $directory = dirname($absolutePath);

            if (! is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            file_put_contents($absolutePath, $contents);
        }

        return realpath($projectPath) ?: $projectPath;
    }
}

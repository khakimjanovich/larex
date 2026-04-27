<?php

namespace Tests\Feature\ArchEngine;

use App\ArchEngine\TargetProject\TargetProjectDetector;
use PHPUnit\Framework\Attributes\Test;
use Random\RandomException;
use Tests\TestCase;

class TargetProjectDetectorTest extends TestCase
{
    #[Test]
    public function it_detects_a_laravel_application(): void
    {
        $projectPath = $this->makeProject([
            'composer.json' => json_encode([
                'name' => 'acme/app',
                'type' => 'project',
                'require' => [
                    'php' => '^8.4',
                    'laravel/framework' => '^13.0',
                ],
                'require-dev' => [
                    'laravel/pint' => '^1.0',
                    'phpunit/phpunit' => '^12.0',
                ],
            ], JSON_THROW_ON_ERROR),
            'artisan' => '',
            'bootstrap/app.php' => '<?php',
        ]);

        $facts = (new TargetProjectDetector)->detect($projectPath);

        $this->assertSame('target-project-facts-v1', $facts->schemaVersion);
        $this->assertSame($projectPath, $facts->path);
        $this->assertSame('laravel_app', $facts->projectType);
        $this->assertSame('acme/app', $facts->composerName);
        $this->assertSame('^8.4', $facts->phpConstraint);
        $this->assertSame('^13.0', $facts->laravelVersion);
        $this->assertContains('laravel/framework', $facts->installedPackages);
        $this->assertContains('pint', $facts->devTools);
        $this->assertSame('phpunit', $facts->testRunner);
        $this->assertContains('target.composer_json', $facts->evidenceRefs);
        $this->assertContains('target.artisan', $facts->evidenceRefs);
    }

    #[Test]
    public function it_detects_a_laravel_package(): void
    {
        $projectPath = $this->makeProject([
            'composer.json' => json_encode([
                'name' => 'acme/laravel-widget',
                'type' => 'library',
                'require' => [
                    'php' => '^8.3',
                    'illuminate/support' => '^13.0',
                ],
                'require-dev' => [
                    'orchestra/testbench' => '^11.0',
                    'pestphp/pest' => '^4.0',
                ],
                'extra' => [
                    'laravel' => [
                        'providers' => [
                            'Acme\\Widget\\WidgetServiceProvider',
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        ]);

        $facts = (new TargetProjectDetector)->detect($projectPath);

        $this->assertSame('laravel_package', $facts->projectType);
        $this->assertSame('acme/laravel-widget', $facts->composerName);
        $this->assertSame('^13.0', $facts->laravelVersion);
        $this->assertContains('pest', $facts->devTools);
        $this->assertSame('pest', $facts->testRunner);
        $this->assertContains('target.composer_extra_laravel', $facts->evidenceRefs);
    }

    #[Test]
    public function it_detects_a_composer_php_package(): void
    {
        $projectPath = $this->makeProject([
            'composer.json' => json_encode([
                'name' => 'acme/plain-package',
                'type' => 'library',
                'require' => [
                    'php' => '^8.2',
                    'symfony/console' => '^7.0',
                ],
                'require-dev' => [
                    'phpunit/phpunit' => '^12.0',
                ],
            ], JSON_THROW_ON_ERROR),
        ]);

        $facts = (new TargetProjectDetector)->detect($projectPath);

        $this->assertSame('php_package', $facts->projectType);
        $this->assertSame('acme/plain-package', $facts->composerName);
        $this->assertNull($facts->laravelVersion);
        $this->assertSame('phpunit', $facts->testRunner);
        $this->assertContains('target.composer_json', $facts->evidenceRefs);
    }

    #[Test]
    public function it_returns_unknown_when_evidence_is_insufficient(): void
    {
        $projectPath = $this->makeProject([
            'README.md' => '# No Composer evidence',
        ]);

        $facts = (new TargetProjectDetector)->detect($projectPath);

        $this->assertSame('unknown', $facts->projectType);
        $this->assertNull($facts->composerName);
        $this->assertSame([], $facts->installedPackages);
        $this->assertContains('target.missing_composer_json', $facts->evidenceRefs);
    }

    #[Test]
    public function it_serializes_target_project_facts_to_the_required_contract(): void
    {
        $projectPath = $this->makeProject([
            'composer.json' => json_encode([
                'name' => 'acme/app',
                'require' => [
                    'php' => '^8.4',
                    'laravel/framework' => '^13.0',
                ],
            ], JSON_THROW_ON_ERROR),
            'artisan' => '',
        ]);

        $payload = (new TargetProjectDetector)->detect($projectPath)->toArray();

        $this->assertSame([
            'schema_version',
            'path',
            'project_type',
            'composer_name',
            'php_constraint',
            'laravel_version',
            'installed_packages',
            'dev_tools',
            'test_runner',
            'evidence_refs',
        ], array_keys($payload));
    }

    /**
     * @param array<string, string> $files
     * @throws RandomException
     */
    private function makeProject(array $files): string
    {
        $projectPath = sys_get_temp_dir().'/larex-target-'.bin2hex(random_bytes(8));

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

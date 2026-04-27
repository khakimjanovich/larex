<?php

namespace Tests\Feature\ArchEngine;

use App\ArchEngine\Tools\CodebaseReader;
use PHPUnit\Framework\Attributes\Test;
use Random\RandomException;
use Tests\TestCase;

class CodebaseReaderTest extends TestCase
{
    private string $tempDir;

    /**
     * @throws RandomException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir().'/larex-codebase-'.bin2hex(random_bytes(8));
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->tempDir);
    }

    #[Test]
    public function it_lists_files_recursively(): void
    {
        file_put_contents($this->tempDir.'/Foo.php', '<?php');
        mkdir($this->tempDir.'/sub', 0777, true);
        file_put_contents($this->tempDir.'/sub/Bar.php', '<?php');
        file_put_contents($this->tempDir.'/readme.md', '# Readme');

        $files = (new CodebaseReader)->listFiles($this->tempDir);

        $this->assertCount(3, $files);
        $this->assertContains($this->tempDir.'/Foo.php', $files);
        $this->assertContains($this->tempDir.'/sub/Bar.php', $files);
        $this->assertContains($this->tempDir.'/readme.md', $files);
    }

    #[Test]
    public function it_filters_by_extension(): void
    {
        file_put_contents($this->tempDir.'/Foo.php', '<?php');
        file_put_contents($this->tempDir.'/readme.md', '# Readme');
        file_put_contents($this->tempDir.'/config.json', '{}');

        $files = (new CodebaseReader)->listFiles($this->tempDir, ['php']);

        $this->assertCount(1, $files);
        $this->assertContains($this->tempDir.'/Foo.php', $files);
    }

    #[Test]
    public function it_ignores_vendor_and_git_directories(): void
    {
        file_put_contents($this->tempDir.'/App.php', '<?php');
        mkdir($this->tempDir.'/vendor/laravel', 0777, true);
        file_put_contents($this->tempDir.'/vendor/laravel/lib.php', '<?php');
        mkdir($this->tempDir.'/.git', 0777, true);
        file_put_contents($this->tempDir.'/.git/config', '');

        $files = (new CodebaseReader)->listFiles($this->tempDir, ['php']);

        $this->assertCount(1, $files);
        $this->assertContains($this->tempDir.'/App.php', $files);
    }

    #[Test]
    public function it_returns_empty_array_for_nonexistent_path(): void
    {
        $files = (new CodebaseReader)->listFiles('/nonexistent/path');

        $this->assertSame([], $files);
    }

    #[Test]
    public function it_reads_a_file(): void
    {
        file_put_contents($this->tempDir.'/Foo.php', '<?php echo "hello";');

        $contents = (new CodebaseReader)->readFile($this->tempDir.'/Foo.php');

        $this->assertSame('<?php echo "hello";', $contents);
    }

    #[Test]
    public function it_returns_empty_string_for_missing_file(): void
    {
        $contents = (new CodebaseReader)->readFile('/nonexistent/file.php');

        $this->assertSame('', $contents);
    }

    #[Test]
    public function it_searches_for_a_pattern_across_files(): void
    {
        file_put_contents($this->tempDir.'/Foo.php', "<?php\nclass Foo {}\nclass Bar {}");
        file_put_contents($this->tempDir.'/Baz.php', "<?php\nclass Baz {}");

        $results = (new CodebaseReader)->searchPattern('class ', $this->tempDir);

        $this->assertCount(3, $results);

        $matches = array_column($results, 'match');
        $this->assertContains('class Foo {}', $matches);
        $this->assertContains('class Bar {}', $matches);
        $this->assertContains('class Baz {}', $matches);
    }

    #[Test]
    public function it_returns_correct_file_and_line_number_in_search_results(): void
    {
        file_put_contents($this->tempDir.'/Foo.php', "<?php\n// comment\nclass Foo {}");

        $results = (new CodebaseReader)->searchPattern('class Foo', $this->tempDir);

        $this->assertCount(1, $results);
        $this->assertSame($this->tempDir.'/Foo.php', $results[0]['file']);
        $this->assertSame(3, $results[0]['line']);
        $this->assertSame('class Foo {}', $results[0]['match']);
    }

    #[Test]
    public function it_returns_empty_array_when_pattern_not_found(): void
    {
        file_put_contents($this->tempDir.'/Foo.php', '<?php echo "nothing";');

        $results = (new CodebaseReader)->searchPattern('interface Nope', $this->tempDir);

        $this->assertSame([], $results);
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

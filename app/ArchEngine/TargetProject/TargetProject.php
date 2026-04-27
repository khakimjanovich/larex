<?php

namespace App\ArchEngine\TargetProject;

final readonly class TargetProject
{
    public const LaravelApp = 'laravel_app';

    public const LaravelPackage = 'laravel_package';

    public const PhpPackage = 'php_package';

    public const Unknown = 'unknown';

    public function __construct(
        public string $path,
        public string $type,
    ) {}
}

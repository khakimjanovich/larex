<?php

namespace App\ArchEngine\Tools;

final class RequirementFileReader
{
    public function exists(string $path): bool
    {
        return is_file($path);
    }

    public function read(string $path): ?string
    {
        $contents = file_get_contents($path);

        return $contents === false ? null : $contents;
    }
}

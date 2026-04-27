<?php

namespace App\ArchEngine\Workers;

final readonly class PatchWorkerResult
{
    /**
     * @param  list<string>  $appliedMutations
     * @param  list<string>  $errors
     */
    public function __construct(
        public bool $success,
        public array $appliedMutations,
        public array $errors,
    ) {}

    /** @param  list<string>  $appliedMutations */
    public static function succeeded(array $appliedMutations): self
    {
        return new self(success: true, appliedMutations: $appliedMutations, errors: []);
    }

    /** @param  list<string>  $appliedMutations */
    public static function failed(array $appliedMutations, string $error): self
    {
        return new self(success: false, appliedMutations: $appliedMutations, errors: [$error]);
    }

    public static function blocked(string $reason): self
    {
        return new self(success: false, appliedMutations: [], errors: [$reason]);
    }
}

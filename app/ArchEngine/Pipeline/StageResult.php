<?php

namespace App\ArchEngine\Pipeline;

final readonly class StageResult
{
    public const Succeeded = 'succeeded';

    public const Failed = 'failed';

    public const Blocked = 'blocked';

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $evidenceRefs
     * @param  list<string>  $warnings
     * @param  list<string>  $errors
     */
    public function __construct(
        public string $status,
        public array $payload,
        public array $evidenceRefs,
        public array $warnings,
        public array $errors,
        public bool $recoverable = false,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $evidenceRefs
     * @param  list<string>  $warnings
     */
    public static function succeeded(array $payload = [], array $evidenceRefs = [], array $warnings = []): self
    {
        return new self(
            status: self::Succeeded,
            payload: $payload,
            evidenceRefs: $evidenceRefs,
            warnings: $warnings,
            errors: [],
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $evidenceRefs
     * @param  list<string>  $warnings
     * @param  list<string>  $errors
     */
    public static function failed(
        array $payload = [],
        array $evidenceRefs = [],
        array $warnings = [],
        array $errors = [],
        bool $recoverable = false,
    ): self {
        return new self(
            status: self::Failed,
            payload: $payload,
            evidenceRefs: $evidenceRefs,
            warnings: $warnings,
            errors: $errors,
            recoverable: $recoverable,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $evidenceRefs
     * @param  list<string>  $warnings
     * @param  list<string>  $errors
     */
    public static function blocked(
        array $payload = [],
        array $evidenceRefs = [],
        array $warnings = [],
        array $errors = [],
        bool $recoverable = false,
    ): self {
        return new self(
            status: self::Blocked,
            payload: $payload,
            evidenceRefs: $evidenceRefs,
            warnings: $warnings,
            errors: $errors,
            recoverable: $recoverable,
        );
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::Succeeded;
    }

    public function shouldStopPipeline(): bool
    {
        return ! $this->isSuccessful() && ! $this->recoverable;
    }
}

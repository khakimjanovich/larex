<?php

namespace App\ArchEngine\DTO;

use App\ArchEngine\Enums\MutationType;

final readonly class Mutation
{
    public function __construct(
        public MutationType $type,
        public ?string $path = null,
        public ?string $content = null,
        public ?string $oldString = null,
        public ?string $newString = null,
        public ?string $command = null,
    ) {}

    public static function createFile(string $path, string $content): self
    {
        return new self(type: MutationType::CreateFile, path: $path, content: $content);
    }

    public static function editFile(string $path, string $oldString, string $newString): self
    {
        return new self(type: MutationType::EditFile, path: $path, oldString: $oldString, newString: $newString);
    }

    public static function runCommand(string $command): self
    {
        return new self(type: MutationType::RunCommand, command: $command);
    }

    /**
     * @return array{type: string, path: string|null, content: string|null, old_string: string|null, new_string: string|null, command: string|null}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'path' => $this->path,
            'content' => $this->content,
            'old_string' => $this->oldString,
            'new_string' => $this->newString,
            'command' => $this->command,
        ];
    }

    /**
     * @param  array{type: string, path?: string|null, content?: string|null, old_string?: string|null, new_string?: string|null, command?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: MutationType::from((string) ($data['type'] ?? '')),
            path: isset($data['path']) ? (string) $data['path'] : null,
            content: isset($data['content']) ? (string) $data['content'] : null,
            oldString: isset($data['old_string']) ? (string) $data['old_string'] : null,
            newString: isset($data['new_string']) ? (string) $data['new_string'] : null,
            command: isset($data['command']) ? (string) $data['command'] : null,
        );
    }
}

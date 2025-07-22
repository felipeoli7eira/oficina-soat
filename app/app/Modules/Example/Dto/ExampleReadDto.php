<?php

declare(strict_types=1);

namespace App\Modules\Example\Dto;

class ExampleReadDto
{
    public function __construct(public readonly string $message) {}

    public function asArray(): array
    {
        return [
            'message' => $this->message
        ];
    }

    public function filled(): array
    {
        return array_map(fn($value) => !is_null($value), $this->asArray());
    }
}

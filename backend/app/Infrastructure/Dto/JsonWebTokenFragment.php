<?php

declare(strict_types=1);

namespace App\Infrastructure\Dto;

class JsonWebTokenFragment
{
    public function __construct(
        public readonly string $sub,
        public readonly string $iss,
        public readonly string $aud,
        public readonly int $iat,
        public readonly int $exp,
        public readonly int $nbf,
    ) {}

    public function toAssociativeArray(): array
    {
        return [
            'sub' => $this->sub,
            'iss' => $this->iss,
            'aud' => $this->aud,
            'iat' => $this->iat,
            'exp' => $this->exp,
            'nbf' => $this->nbf,
        ];
    }
}

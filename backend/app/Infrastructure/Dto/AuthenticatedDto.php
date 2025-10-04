<?php

declare(strict_types=1);

namespace App\Infrastructure\Dto;

use App\Domain\Entity\Usuario\Entidade;

class AuthenticatedDto
{
    public function __construct(
        public readonly Entidade $usuario,
        public readonly string $token,
        public readonly string $tokenType
    ) {}

    public function toAssociativeArray(): array
    {
        return [
            'user'       => $this->usuario->toHttpResponse(),
            'token'      => $this->token,
            'token_type' => $this->tokenType,
        ];
    }
}

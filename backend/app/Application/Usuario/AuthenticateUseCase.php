<?php

declare(strict_types=1);

namespace App\Application\Usuario;

use App\Infrastructure\Service\JsonWebTokenHandler\JsonWebTokenHandlerContract;
use App\Domain\Usuario\Entity;

final class AuthenticateUseCase
{
    public function __construct(public readonly Entity $entity, public readonly JsonWebTokenHandlerContract $tokenHandler) {}

    public function handle(): string
    {
        $token = $this->tokenHandler->generate($this->entity);

        return $token;
    }
}

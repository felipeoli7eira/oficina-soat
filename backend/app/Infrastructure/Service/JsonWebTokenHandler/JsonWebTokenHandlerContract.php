<?php

declare(strict_types=1);

namespace App\Infrastructure\Service\JsonWebTokenHandler;

use App\Domain\Usuario\Entity;

interface JsonWebTokenHandlerContract
{
    public function generate(Entity $usuario): string;
    public function decode(string $token): ?array;
    // public function validate(string $token): bool;
}

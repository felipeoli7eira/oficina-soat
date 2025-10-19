<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Usuario\Entity;

interface TokenHandlerContract
{
    public function generate(Entity $usuario): string;
    public function decode(string $token): ?array;
    // public function validate(string $token): bool;
}

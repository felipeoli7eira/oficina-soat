<?php

declare(strict_types=1);

namespace App\Signature;

use App\Domain\Entity\Usuario\Entidade;

interface AuthServiceInterface
{
    public function attempt(string $email, string $plainTextPassword): ?Entidade;
    public function check(): bool;
    public function user(): ?Entidade;
}

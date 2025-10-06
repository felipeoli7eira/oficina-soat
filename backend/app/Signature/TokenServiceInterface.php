<?php

declare(strict_types=1);

namespace App\Signature;

use App\Infrastructure\Dto\JsonWebTokenFragment;

interface TokenServiceInterface
{
    public function generate(array $claims): string;
    public function validate(string $token): ?JsonWebTokenFragment;
    public function refresh(string $token): string;
    public function invalidate(string $token): void;
}

<?php

declare(strict_types=1);

namespace App\Signature;

interface TokenServiceInterface
{
    public function generate(array $claims): string;
    public function validate(string $token): ?array;
    public function refresh(string $token): string;
    public function invalidate(string $token): void;
}

<?php

namespace App\Infrastructure\Service\UuidGenerator;

interface UuidGeneratorContract
{
    public function generate(): string;
}

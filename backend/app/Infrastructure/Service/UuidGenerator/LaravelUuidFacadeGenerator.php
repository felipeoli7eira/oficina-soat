<?php

namespace App\Infrastructure\Service\UuidGenerator;

class LaravelUuidFacadeGenerator implements UuidGeneratorContract
{
    public function generate(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }
}

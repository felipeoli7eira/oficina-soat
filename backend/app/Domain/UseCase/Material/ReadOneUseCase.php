<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Material;

use App\Infrastructure\Gateway\MaterialGateway;

class ReadOneUseCase
{
    public function __construct(public readonly string $uuid) {}

    public function exec(MaterialGateway $gateway): ?array
    {
        if (empty($this->uuid)) {
            return null;
        }

        $res = $gateway->encontrarPorIdentificadorUnico($this->uuid, 'uuid');

        return $res?->toHttpResponse();
    }
}

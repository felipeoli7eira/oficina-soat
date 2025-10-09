<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Cliente;

use App\Infrastructure\Gateway\ClienteGateway;

class ReadOneUseCase
{
    public function __construct(public readonly string $uuid) {}

    public function exec(ClienteGateway $gateway): ?array
    {
        if (empty($this->uuid)) {
            return null;
        }

        $res = $gateway->encontrarPorIdentificadorUnico($this->uuid, 'uuid');

        return $res?->toHttpResponse();
    }
}

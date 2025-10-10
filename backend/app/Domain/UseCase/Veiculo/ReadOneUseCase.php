<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Veiculo;

use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;

class ReadOneUseCase
{
    public function __construct(public readonly string $uuid) {}

    public function exec(VeiculoGateway $gateway, ClienteGateway $clienteGateway): ?array
    {
        if (empty($this->uuid)) {
            return null;
        }

        $res = $gateway->encontrarPorIdentificadorUnico($this->uuid, 'uuid');

        return $res?->toHttpResponse();
    }
}

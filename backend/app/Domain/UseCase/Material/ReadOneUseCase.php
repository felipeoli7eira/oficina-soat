<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Servico;

use App\Infrastructure\Gateway\ServicoGateway;

class ReadOneUseCase
{
    public function __construct(public readonly string $uuid) {}

    public function exec(ServicoGateway $gateway): ?array
    {
        if (empty($this->uuid)) {
            return null;
        }

        $res = $gateway->encontrarPorIdentificadorUnico($this->uuid, 'uuid');

        return $res?->toHttpResponse();
    }
}

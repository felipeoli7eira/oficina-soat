<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Material;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;

class DeleteUseCase
{
    public function __construct(public readonly MaterialGateway $gateway) {}

    public function exec(string $uuid): bool
    {
        // regras de negocio

        if (is_null($this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid'))) {
            throw new DomainHttpException('Não encontrado com o identificador informado', 400);
        }

        return $this->gateway->deletar($uuid);
    }
}

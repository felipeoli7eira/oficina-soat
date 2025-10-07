<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Servico;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;

class DeleteUseCase
{
    public function __construct(public readonly ServicoGateway $gateway) {}

    public function exec(string $uuid): bool
    {
        // regras de negocio

        if (is_null($this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid'))) {
            throw new DomainHttpException('Serviço não encontrado', 400);
        }

        return $this->gateway->deletar($uuid);
    }
}

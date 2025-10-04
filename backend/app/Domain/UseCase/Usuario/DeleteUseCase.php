<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\UsuarioGateway;

class DeleteUseCase
{
    public function __construct(public readonly UsuarioGateway $gateway) {}

    public function exec(string $uuid): bool
    {
        // regras de negocio

        if (is_null($this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid'))) {
            throw new DomainHttpException('Usuário não encontrado', 400);
        }

        return $this->gateway->deletar($uuid);
    }
}

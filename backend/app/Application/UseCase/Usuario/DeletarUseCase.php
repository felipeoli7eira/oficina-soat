<?php

declare(strict_types=1);

namespace App\Application\UseCase\Usuario;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\UsuarioGateway;

class DeletarUseCase
{
    public function __construct(public readonly UsuarioGateway $gateway) {}

    public function deletar(string $uuid): bool
    {
        // regras de negocio

        if (is_null($this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid'))) {
            throw new DomainHttpException('Usuário não encontrado', 400);
        }

        return $this->gateway->deletar($uuid);
    }
}

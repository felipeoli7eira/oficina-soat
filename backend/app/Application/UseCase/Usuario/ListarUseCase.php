<?php

declare(strict_types=1);

namespace App\Application\UseCase\Usuario;

use App\Infrastructure\Gateway\UsuarioGateway;
use App\Infrastructure\Dto\UsuarioDto;
use App\Domain\Usuario\Entidade;
use App\Exception\DomainHttpException;

class ListarUseCase
{
    public function __construct(public readonly UsuarioGateway $gateway) {}

    public function listar(): array
    {
        $res = $this->gateway->listar();

        return $res;
    }
}

<?php

declare(strict_types=1);

namespace App\Application\UseCase\Usuario;

use App\Infrastructure\Gateway\UsuarioGateway;

class ListarUseCase
{
    public function __construct() {}

    public function listar(UsuarioGateway $gateway): array
    {
        $res = $gateway->listar();

        return $res;
    }
}

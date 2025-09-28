<?php

declare(strict_types=1);

namespace App\Application\UseCase\Usuario;

use App\Infrastructure\Gateway\UsuarioGateway;

class ReadUseCase
{
    public function __construct() {}

    public function exec(UsuarioGateway $gateway): array
    {
        $res = $gateway->listar();

        return $res;
    }
}

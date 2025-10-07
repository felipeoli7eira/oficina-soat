<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Servico;

use App\Infrastructure\Gateway\ServicoGateway;

class ReadUseCase
{
    public function __construct() {}

    public function exec(ServicoGateway $gateway): array
    {
        $res = $gateway->listar();

        return $res;
    }
}

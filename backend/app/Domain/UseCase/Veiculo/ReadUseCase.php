<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;

class ReadUseCase
{
    public function __construct() {}

    public function exec(VeiculoGateway $gateway, ClienteGateway $clienteGateway): array
    {
        $res = $gateway->listar();

        return array_map(function ($r) {
            $entidade = new Entidade(
                uuid: $r['uuid'],
                marca: $r['marca'],
                modelo: $r['modelo'],
                placa: $r['placa'],
                ano: $r['ano'],
                clienteId: $r['cliente_id'],
                criadoEm: new DateTimeImmutable($r['criado_em']),
                atualizadoEm: new DateTimeImmutable($r['atualizado_em']),
            );

            return $entidade->toHttpResponse();
        }, $res);
    }
}

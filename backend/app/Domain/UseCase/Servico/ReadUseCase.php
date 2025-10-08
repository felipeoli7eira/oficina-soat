<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;

class ReadUseCase
{
    public function __construct() {}

    public function exec(ServicoGateway $gateway): array
    {
        $res = $gateway->listar();

        return array_map(function ($r) {
            $entidade = new Entidade(
                uuid: $r['uuid'],
                nome: $r['nome'],
                valor: $r['valor'],
                criadoEm: new DateTimeImmutable($r['criado_em']),
                atualizadoEm: new DateTimeImmutable($r['atualizado_em']),
            );

            return $entidade->toHttpResponse();
        }, $res);
    }
}

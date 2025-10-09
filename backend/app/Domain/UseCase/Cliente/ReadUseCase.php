<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Infrastructure\Gateway\ClienteGateway;
use DateTimeImmutable;

class ReadUseCase
{
    public function __construct() {}

    public function exec(ClienteGateway $gateway): array
    {
        $res = $gateway->listar();

        return array_map(function ($r) {
            $entidade = new Entidade(
                uuid: $r['uuid'],
                nome: $r['nome'],
                documento: $r['documento'],
                email: $r['email'],
                fone: $r['fone'],
                criadoEm: new DateTimeImmutable($r['criado_em']),
                atualizadoEm: new DateTimeImmutable($r['atualizado_em']),
            );

            return $entidade->toHttpResponse();
        }, $res);
    }
}

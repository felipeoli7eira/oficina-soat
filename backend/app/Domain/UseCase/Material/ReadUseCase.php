<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;

class ReadUseCase
{
    public function __construct() {}

    public function exec(MaterialGateway $gateway): array
    {
        $res = $gateway->listar();

        return array_map(function ($r) {
            $entidade = new Entidade(
                uuid: $r['uuid'],
                nome: $r['nome'],
                gtin: $r['gtin'],
                estoque: $r['estoque'],
                sku: $r['sku'],
                descricao: $r['descricao'],
                preco_custo: $r['preco_custo'],
                preco_venda: $r['preco_venda'],
                preco_uso_interno: $r['preco_uso_interno'],
                criadoEm: new DateTimeImmutable($r['criado_em']),
                atualizadoEm: new DateTimeImmutable($r['atualizado_em']),
            );

            return $entidade->toHttpResponse();
        }, $res);
    }
}

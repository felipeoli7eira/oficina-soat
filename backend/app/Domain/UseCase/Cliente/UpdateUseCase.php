<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;

class UpdateUseCase
{
    public function __construct(public readonly MaterialGateway $gateway) {}

    public function exec(string $uuid, array $novosDados): Entidade
    {
        if (empty($uuid)) {
            throw new DomainHttpException('identificador único não informado', 400);
        }

        $existente = $this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid');

        if (is_null($existente)) {
            throw new DomainHttpException('Não encontrado(a)', 404);
        }

        $entidadeAtual = new Entidade(
            uuid: $existente->uuid,
            nome: $existente->nome,
            gtin: $existente->gtin,
            estoque: $existente->estoque,
            sku: $existente->sku,
            descricao: $existente->descricao,
            preco_custo: $existente->preco_custo,
            preco_venda: $existente->preco_venda,
            preco_uso_interno: $existente->preco_uso_interno,
            criadoEm: $existente->criadoEm,
            atualizadoEm: $existente->atualizadoEm,
            deletadoEm: $existente->deletadoEm instanceof DateTimeImmutable ? $existente->deletadoEm : null,
        );

        $entidadeAtual->atualizar($novosDados);

        $update = $this->gateway->atualizar($uuid, $entidadeAtual->toUpdateDataArray());

        if (! is_array($update)) {
            throw new DomainHttpException('Erro ao atualizar usuário', 500);
        }

        return new Entidade(
            uuid: $update['uuid'],
            nome: $update['nome'],
            gtin: $update['gtin'],
            estoque: $update['estoque'],
            sku: $update['sku'],
            descricao: $update['descricao'],
            preco_custo: $update['preco_custo'],
            preco_venda: $update['preco_venda'],
            preco_uso_interno: $update['preco_uso_interno'],
            criadoEm: new DateTimeImmutable($update['criado_em']),
            atualizadoEm: new DateTimeImmutable($update['atualizado_em']),
            deletadoEm: $update['deletado_em'] ? new DateTimeImmutable($update['deletado_em']) : null,
        );
    }
}

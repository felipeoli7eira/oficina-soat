<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Infrastructure\Gateway\OrdemGateway;
use DateTimeImmutable;

class ReadUseCase
{
    public function __construct() {}

    public function exec(OrdemGateway $gateway, array $filters = []): array
    {
        $dados = $gateway->listar($filters);

        return array_map(function ($d) {
            $entidade = new Entidade(
                uuid: $d['uuid'],
                cliente: new ClienteEntidade(
                    $d['cliente']['uuid'],
                    $d['cliente']['nome'],
                    $d['cliente']['documento'],
                    $d['cliente']['email'],
                    $d['cliente']['fone'],
                    new DateTimeImmutable($d['cliente']['criado_em']),
                    new DateTimeImmutable($d['cliente']['atualizado_em']),
                ),
                veiculo: new VeiculoEntidade(
                    $d['veiculo']['uuid'],
                    $d['veiculo']['marca'],
                    $d['veiculo']['modelo'],
                    $d['veiculo']['placa'],
                    $d['veiculo']['ano'],
                    $d['veiculo']['cliente_id'],
                    new DateTimeImmutable($d['veiculo']['criado_em']),
                    new DateTimeImmutable($d['veiculo']['atualizado_em']),
                ),
                descricao: $d['descricao'],
                status: $d['status'],
                dtAbertura: new DateTimeImmutable($d['dt_abertura']),
                dtFinalizacao: $d['dt_finalizacao'] ? new DateTimeImmutable($d['dt_finalizacao']) : null,
                dtAtualizacao: $d['dt_atualizacao'] ? new DateTimeImmutable($d['dt_atualizacao']) : null,
                servicos: $d['servicos'],
                materiais: $d['materiais'],
            );

            return $entidade->toExternal();
        }, $dados);
    }
}

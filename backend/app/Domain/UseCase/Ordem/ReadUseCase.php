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

    public function exec(OrdemGateway $gateway): array
    {
        $dados = $gateway->listar();

        return array_map(function ($d) {
            $entidade = new Entidade(
                $d['uuid'],
                new ClienteEntidade(
                    $d['cliente']['uuid'],
                    $d['cliente']['nome'],
                    $d['cliente']['documento'],
                    $d['cliente']['email'],
                    $d['cliente']['fone'],
                    new DateTimeImmutable($d['cliente']['criado_em']),
                    new DateTimeImmutable($d['cliente']['atualizado_em']),
                ),
                new VeiculoEntidade(
                    $d['veiculo']['uuid'],
                    $d['veiculo']['marca'],
                    $d['veiculo']['modelo'],
                    $d['veiculo']['placa'],
                    $d['veiculo']['ano'],
                    $d['veiculo']['cliente_id'],
                    new DateTimeImmutable($d['veiculo']['criado_em']),
                    new DateTimeImmutable($d['veiculo']['atualizado_em']),
                ),
                $d['descricao'],
                $d['status'],
                new DateTimeImmutable($d['dt_abertura']),
                $d['dt_finalizacao'] ? new DateTimeImmutable($d['dt_finalizacao']) : null,
                $d['dt_atualizacao'] ? new DateTimeImmutable($d['dt_atualizacao']) : null,
            );

            return $entidade->toExternal();
        }, $dados);
    }
}

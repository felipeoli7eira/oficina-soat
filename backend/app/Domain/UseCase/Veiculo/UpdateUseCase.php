<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;
use DateTimeImmutable;

class UpdateUseCase
{
    public function __construct(public readonly VeiculoGateway $gateway) {}

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
            $existente->uuid,
            $existente->marca,
            $existente->modelo,
            $existente->placa,
            $existente->ano,
            0, // nao é possivel alterar o cliente do veiculo, entao pode passar zero ou qualquer numero pois o ->toUpdateDataArray() nao devolve o cliente_id
            $existente->criadoEm,
            $existente->atualizadoEm,
            $existente->deletadoEm instanceof DateTimeImmutable ? $existente->deletadoEm : null,
        );

        $entidadeAtual->atualizar($novosDados);

        $update = $this->gateway->atualizar($uuid, $entidadeAtual->toUpdateDataArray());

        if (! is_array($update)) {
            throw new DomainHttpException('Erro na atualização', 500);
        }

        return new Entidade(
            $update['uuid'],
            $update['marca'],
            $update['modelo'],
            $update['placa'],
            $update['ano'],
            0,
            new DateTimeImmutable($update['criado_em']),
            new DateTimeImmutable($update['atualizado_em']),
            $update['deletado_em'] ? new DateTimeImmutable($update['deletado_em']) : null,
        );
    }
}

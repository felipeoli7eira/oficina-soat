<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\OrdemGateway;
use DateTimeImmutable;

class UpdateStatusUseCase
{
    public function __construct(public readonly OrdemGateway $gateway) {}

    public function exec(string $uuid, string $novoStatus): Entidade
    {
        if (empty($uuid)) {
            throw new DomainHttpException('identificador único não informado', 400);
        }

        $existente = $this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid');

        if ($existente instanceof Entidade === false) {
            throw new DomainHttpException('Não encontrado(a)', 404);
        }

        $statusDisponiveis = [
            Entidade::STATUS_RECEBIDA,
            Entidade::STATUS_EM_DIAGNOSTICO,
            Entidade::STATUS_AGUARDANDO_APROVACAO,
            Entidade::STATUS_APROVADA,
            Entidade::STATUS_REPROVADA,
            Entidade::STATUS_CANCELADA,
            Entidade::STATUS_EM_EXECUCAO,
            Entidade::STATUS_FINALIZADA,
            Entidade::STATUS_ENTREGUE,
        ];

        if (!in_array($novoStatus, $statusDisponiveis)) {
            throw new DomainHttpException('Opções de status disponíveis: ' . implode(', ', $statusDisponiveis), 400);
        }

        $update = $this->gateway->atualizarStatus($uuid, $novoStatus);

        if (! is_array($update)) {
            throw new DomainHttpException('Erro na atualização', 500);
        }

        return new Entidade(
            $update['uuid'],
            new ClienteEntidade(
                $update['cliente']['uuid'],
                $update['cliente']['nome'],
                $update['cliente']['documento'],
                $update['cliente']['email'],
                $update['cliente']['fone'],
                new DateTimeImmutable($update['cliente']['criado_em']),
                new DateTimeImmutable($update['cliente']['atualizado_em']),
                $update['cliente']['deletado_em'] ? new DateTimeImmutable($update['cliente']['deletado_em']) : null,
            ),
            new VeiculoEntidade(
                $update['veiculo']['uuid'],
                $update['veiculo']['marca'],
                $update['veiculo']['modelo'],
                $update['veiculo']['placa'],
                $update['veiculo']['ano'],
                $update['veiculo']['cliente_id'],
                new DateTimeImmutable($update['veiculo']['criado_em']),
                new DateTimeImmutable($update['veiculo']['atualizado_em']),
                $update['veiculo']['deletado_em'] ? new DateTimeImmutable($update['veiculo']['deletado_em']) : null,
            ),
            $update['descricao'],
            $update['status'],
            new DateTimeImmutable($update['dt_abertura']),
            $update['dt_finalizacao'] ? new DateTimeImmutable($update['dt_finalizacao']) : null,
            $update['dt_atualizacao'] ? new DateTimeImmutable($update['dt_atualizacao']) : null,
        );
    }
}

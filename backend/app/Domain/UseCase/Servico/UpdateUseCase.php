<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;

class UpdateUseCase
{
    public function __construct(public readonly ServicoGateway $gateway) {}

    public function exec(string $uuid, string $novoNome, int $novoValor): Entidade
    {
        if (empty($uuid)) {
            throw new DomainHttpException('identificador único não informado', 400);
        }

        if (is_null($this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid'))) {
            throw new DomainHttpException('Não encontrado(a)', 400);
        }

        $entidade = new Entidade(
            uuid: $uuid,
            nome: $novoNome,
            valor: $novoValor,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $update = $this->gateway->atualizar($uuid, $entidade->toCreateDataArray());

        if (! is_array($update)) {
            throw new DomainHttpException('Erro ao atualizar usuário', 500);
        }

        return new Entidade(
            uuid: $update['uuid'],
            nome: $update['nome'],
            valor: $update['valor'],
            criadoEm: new DateTimeImmutable($update['criado_em']),
            atualizadoEm: new DateTimeImmutable($update['atualizado_em']),
            deletadoEm: $update['deletado_em'] ? new DateTimeImmutable($update['deletado_em']) : null,
        );
    }
}

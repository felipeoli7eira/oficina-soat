<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Ordem;

use App\Domain\Entity\Ordem\Entidade as Ordem;
use App\Domain\Entity\Cliente\Entidade as Cliente;
use App\Domain\Entity\Veiculo\Entidade as Veiculo;

use App\Infrastructure\Gateway\OrdemGateway;

use DateTimeImmutable;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;

class CreateUseCase
{
    public function __construct(
        public string $clienteUuid,
        public string $veiculoUuid,
        public ?string $descricao = null,
    ) {}

    public function exec(OrdemGateway $gateway, ClienteGateway $clienteGateway, VeiculoGateway $veiculoGateway): Ordem
    {
        $cliente = $clienteGateway->encontrarPorIdentificadorUnico($this->clienteUuid, 'uuid');
        if ($cliente instanceof Cliente === false) {
            throw new DomainHttpException('Cliente não encontrado', 404);
        }

        $veiculo = $veiculoGateway->encontrarPorIdentificadorUnico($this->veiculoUuid, 'uuid');
        if ($veiculo instanceof Veiculo === false) {
            throw new DomainHttpException('Veículo não encontrado', 404);
        }

        $ordensNaoFinalizadas = $gateway->obterOrdensDoClienteComStatusDiferenteDe($cliente->uuid, Ordem::STATUS_FINALIZADA);

        $countOrdensNaoFinalizadas = count($ordensNaoFinalizadas);

        if ($countOrdensNaoFinalizadas) {
            throw new DomainHttpException("Cliente possui {$countOrdensNaoFinalizadas} ordem(ns) não finalizada(s)", 400);
        }

        $ordem = new Ordem(
            uuid: '',
            cliente: $cliente,
            veiculo: $veiculo,
            descricao: $this->descricao,
            status: Ordem::STATUS_RECEBIDA,
            dtAbertura: new DateTimeImmutable(),
            dtFinalizacao: null,
            dtAtualizacao: null,
        );

        $dadosOrdem = [
            'descricao' => $ordem->descricao,
            'status'    => $ordem->status,
            'dt_abertura' => $ordem->dtAbertura->format('Y-m-d H:i:s'),
        ];

        $cadastro = $gateway->criar($cliente->uuid, $veiculo->uuid, $dadosOrdem);

        if (! is_array($cadastro)) {
            throw new DomainHttpException('Erro ao cadastrar', 500);
        }

        return new Ordem(
            uuid: $cadastro['uuid'],
            cliente: $cliente,
            veiculo: $veiculo,
            descricao: $cadastro['descricao'],
            status: $cadastro['status'],
            dtAbertura: new DateTimeImmutable($cadastro['dt_abertura']),
            dtFinalizacao: null,
            dtAtualizacao: null,
        );
    }
}

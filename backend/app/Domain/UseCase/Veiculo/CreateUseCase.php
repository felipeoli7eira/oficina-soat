<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Infrastructure\Gateway\VeiculoGateway;
use App\Domain\UseCase\Cliente\ReadOneUseCase as ClienteReadOneUseCase;

use DateTimeImmutable;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;

class CreateUseCase
{
    public readonly VeiculoGateway $gateway;

    public function __construct(
        public string $marca,
        public string $modelo,
        public string $placa,
        public int $ano,
        public string $clienteUuid,
    ) {}

    public function exec(VeiculoGateway $gateway, ClienteGateway $clienteGateway): Entidade
    {
        // regras de negocio, validacoes...

        if (!is_string($this->clienteUuid) || empty($this->clienteUuid)) {
            throw new DomainHttpException('Cliente dono do veículo não informado', 400);
        }

        $cliente = $clienteGateway->encontrarPorIdentificadorUnico($this->clienteUuid, 'uuid');

        if (is_null($cliente)) {
            throw new DomainHttpException('Cliente não encontrado', 404);
        }

        $idNumericoCliente = $clienteGateway->obterIdNumerico($cliente->uuid);

        if ($idNumericoCliente === -1) {
            throw new DomainHttpException('Cliente não encontrado', 404);
        }

        $entidade = new Entidade(
            uuid: '',
            marca: $this->marca,
            modelo: $this->modelo,
            placa: $this->placa,
            ano: $this->ano,
            clienteId: $idNumericoCliente,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        if ($gateway->encontrarPorIdentificadorUnico($this->placa, 'placa') instanceof Entidade) {
            throw new DomainHttpException('Placa não pode ser cadastrada pois já existe um veículo com essa placa', 400);
        }

        $cadastro = $gateway->criar($entidade->toCreateDataArray());

        if (! is_array($cadastro)) {
            throw new DomainHttpException('Erro ao cadastrar', 500);
        }

        return new Entidade(
            uuid: $cadastro['uuid'],
            marca: $cadastro['marca'],
            modelo: $cadastro['modelo'],
            placa: $cadastro['placa'],
            ano: $cadastro['ano'],
            clienteId: $cadastro['cliente_id'],
            criadoEm: new DateTimeImmutable($cadastro['criado_em']),
            atualizadoEm: new DateTimeImmutable($cadastro['atualizado_em']),
        );
    }
}

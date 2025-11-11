<?php

declare(strict_types=1);

namespace App\Application\Veiculo;

use App\Domain\Veiculo\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\VeiculoGateway;
use DateTime;
use RuntimeException;
use \App\Exception\DomainHttpException;

final class ReadUseCase
{
    public function __construct(public readonly VeiculoGateway $gateway) {}

    public function handle(array $readParams = []): array
    {
        if ($this->gateway instanceof VeiculoGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $dados = $this->gateway->read($readParams);

        return array_map(function ($rawData) {

            $domainEntity = new Entity(
                $rawData['uuid'],
                $rawData['marca'],
                $rawData['modelo'],
                $rawData['placa'],
                $rawData['ano'],
                $rawData['cliente_uuid'],

                isset($rawData['cadastrado_em']) ? new DateTime($rawData['cadastrado_em']) : new DateTime(),
                isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
                isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
            );

            return $domainEntity->toExternal();
        }, $dados);
    }
}

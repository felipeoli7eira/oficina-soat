<?php

declare(strict_types=1);

namespace App\Application\Veiculo;

use DateTime;
use App\Domain\Veiculo\Entity;
use App\Exception\DomainHttpException;
use App\Interface\Gateway\VeiculoGateway;

final class ReadOneByUuidUseCase
{
    public function __construct(public readonly VeiculoGateway $gateway) {}

    public function handle(string $uuid): array
    {
        $repoData = $this->gateway->findOneBy('uuid', $uuid);

        if ($repoData === null) {
            throw new DomainHttpException('Não encontrado(a)', 404);
        }

        $domainEntity = new Entity(
            $repoData['uuid'],
            $repoData['marca'],
            $repoData['modelo'],
            $repoData['placa'],
            $repoData['ano'],
            $repoData['cliente_uuid'],

            isset($repoData['criado_em']) ? new DateTime($repoData['criado_em']) : new DateTime(),
            isset($repoData['atualizado_em']) ? new DateTime($repoData['atualizado_em']) : null,
        );

        return $domainEntity->toExternal();
    }
}

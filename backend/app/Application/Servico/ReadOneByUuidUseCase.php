<?php

declare(strict_types=1);

namespace App\Application\Cliente;

use DateTime;
use RuntimeException;
use App\Domain\Cliente\Entity;
use App\Exception\DomainHttpException;
use App\Interface\Gateway\ClienteGateway;

final class ReadOneByUuidUseCase
{
    public function __construct(public readonly ClienteGateway $gateway) {}

    public function handle(string $uuid): array
    {
        $rawData = $this->gateway->findOneBy('uuid', $uuid);

        if ($rawData === null) {
            throw new DomainHttpException('Não encontrado(a)', 404);
        }

        $domainEntity = new Entity(
            $rawData['uuid'] ?? '',
            $rawData['nome'],
            $rawData['documento'],
            $rawData['email'],
            $rawData['fone'],

            isset($rawData['cadastrado_em']) ? new DateTime($rawData['cadastrado_em']) : new DateTime(),
            isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
            isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
        );

        return $domainEntity->toExternal();
    }
}

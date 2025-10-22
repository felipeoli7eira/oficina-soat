<?php

declare(strict_types=1);

namespace App\Application\Cliente;

use App\Domain\Cliente\Entity;
use App\Interface\Gateway\ClienteGateway;
use DateTime;
use RuntimeException;
use App\Exception\DomainHttpException;

final class ReadUseCase
{
    public function __construct(public readonly ClienteGateway $gateway) {}

    public function handle(array $readParams = []): array
    {
        $dados = $this->gateway->read($readParams);

        return array_map(function ($rawData) {

            $domainEntity = new Entity(
                $rawData['uuid'] ?? '',
                $rawData['nome'],
                $rawData['documento'],
                $rawData['email'],
                $rawData['fone'],

                isset($rawData['criadodo_em']) ? new DateTime($rawData['criadodo_em']) : new DateTime(),
                isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
                isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
            );

            return $domainEntity->toExternal();
        }, $dados);
    }
}

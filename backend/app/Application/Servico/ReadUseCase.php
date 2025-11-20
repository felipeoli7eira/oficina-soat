<?php

declare(strict_types=1);

namespace App\Application\Servico;

use App\Domain\Servico\Entity;
use App\Interface\Gateway\ServicoGateway;
use DateTime;
use RuntimeException;
use App\Exception\DomainHttpException;

final class ReadUseCase
{
    public function __construct(public readonly ServicoGateway $gateway) {}

    public function handle(array $readParams = []): array
    {
        $dados = $this->gateway->read($readParams);

        return array_map(function ($rawData) {

            $domainEntity = new Entity(
                $rawData['uuid'],
                $rawData['nome'],
                $rawData['valor'],
                $rawData['disponivel'],

                isset($rawData['criadodo_em']) ? new DateTime($rawData['criadodo_em']) : new DateTime(),
                isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
                isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
            );

            return $domainEntity->toExternal();
        }, $dados);
    }
}

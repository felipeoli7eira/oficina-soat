<?php

declare(strict_types=1);

namespace App\Application\Usuario;

use App\Domain\Usuario\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\UsuarioGateway;
use DateTime;
use RuntimeException;

final class ReadOneByUuidUseCase
{
    public function __construct(public readonly UsuarioGateway $gateway) {}

    public function handle(string $uuid): array
    {
        if ($this->gateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $rawData = $this->gateway->findOneBy('uuid', $uuid);

        if (is_null($rawData)) {
            throw new \App\Exception\DomainHttpException('Não encontrado(a)', 404);
        }

        $domainEntity = new Entity(
            $rawData['uuid'] ?? '',
            $rawData['nome'],
            $rawData['email'],
            $rawData['senha'],
            $rawData['ativo'],

            ProfileEnum::tryFrom($rawData['perfil']),

            isset($rawData['cadastrado_em']) ? new DateTime($rawData['cadastrado_em']) : new DateTime(),
            isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
            isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
        );

        return $domainEntity->toExternal();
    }
}

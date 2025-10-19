<?php

declare(strict_types=1);

namespace App\Application\Usuario;

use App\Domain\Usuario\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\UsuarioGateway;
use DateTime;
use RuntimeException;
use \App\Exception\DomainHttpException;

final class DeleteUseCase
{
    private ?UsuarioGateway $gateway = null;

    public function __construct(public readonly string $uuid) {}

    public function useGateway(UsuarioGateway $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function handle(): bool
    {
        if ($this->gateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $rawData = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($rawData === null) {
            throw new DomainHttpException('Não encontrado(a)', 404);
        }

        $domainEntity = new Entity(
            $rawData['uuid'],
            $rawData['nome'],
            $rawData['email'],
            $rawData['senha'],
            $rawData['ativo'],

            ProfileEnum::tryFrom($rawData['perfil']),

            isset($rawData['cadastrado_em']) ? new DateTime($rawData['cadastrado_em']) : new DateTime(),
            isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
            isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
        );

        $domainEntity->delete();

        $res = $this->gateway->delete($domainEntity->asArray());

        return $res;
    }
}

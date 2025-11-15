<?php

declare(strict_types=1);

namespace App\Application\Cliente;

use App\Domain\Cliente\Entity;
use App\Domain\Cliente\ProfileEnum;
use App\Domain\Usuario\ProfileEnum as UsuarioProfileEnum;
use App\Interface\Gateway\ClienteGateway;
use DateTime;
use RuntimeException;
use \App\Exception\DomainHttpException;
use App\Interface\Gateway\UsuarioGateway;

final class DeleteUseCase
{
    private ?ClienteGateway $gateway = null;
    private ?UsuarioGateway $usuarioGateway = null;

    public function __construct(public readonly string $uuid) {}

    public function useGateway(ClienteGateway $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function useUsuarioGateway(UsuarioGateway $gateway): self
    {
        $this->usuarioGateway = $gateway;
        return $this;
    }

    public function handle(string $authenticatedUserUuid): bool
    {
        if (empty(trim($authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        if ($this->gateway instanceof ClienteGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        if ($this->usuarioGateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway de usuário não definido');
        }

        $authenticatedUser = $this->usuarioGateway->findOneBy('uuid', $authenticatedUserUuid);

        if ($authenticatedUser === null) {
            throw new DomainHttpException('O usuário autenticado com o identificador informadas não foi encontrado', 404);
        }

        if (! in_array($authenticatedUser['perfil'], [UsuarioProfileEnum::ADMIN->value, UsuarioProfileEnum::ATENDENTE->value])) {
            throw new DomainHttpException('Você não tem permissão para realizar essa ação.', 404);
        }

        $rawData = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($rawData === null) {
            throw new DomainHttpException('Cliente com o identificador informado não foi encontrado(a)', 404);
        }

        $domainEntity = new Entity(
            $rawData['uuid'],
            $rawData['nome'],
            $rawData['documento'],
            $rawData['email'],
            $rawData['fone'],

            isset($rawData['cadastrado_em']) ? new DateTime($rawData['criado_em']) : new DateTime(),
            isset($rawData['atualizado_em']) ? new DateTime($rawData['atualizado_em']) : null,
            isset($rawData['deletado_em']) ? new DateTime($rawData['deletado_em']) : null,
        );

        $domainEntity->delete();

        $res = $this->gateway->delete($domainEntity->asArray());

        return $res;
    }
}

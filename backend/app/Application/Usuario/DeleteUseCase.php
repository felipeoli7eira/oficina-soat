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

    public function handle(string $authenticatedUserUuid): bool
    {
        if (empty(trim($authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        if ($this->gateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $authenticatedUser = $this->gateway->findOneBy('uuid', $authenticatedUserUuid);

        if ($authenticatedUser === null) {
            throw new DomainHttpException('O usuário com as credenciais informadas não foi encontrado', 404);
        }

        if ($authenticatedUser['perfil'] !== ProfileEnum::ADMIN->value) {
            throw new DomainHttpException('Você não tem permissão para realizar essa ação. Somente um administrador pode finalizar um cadastro.', 404);
        }

        $rawData = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($rawData === null) {
            throw new DomainHttpException('Usuário com o identificador informado não foi encontrado(a)', 404);
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

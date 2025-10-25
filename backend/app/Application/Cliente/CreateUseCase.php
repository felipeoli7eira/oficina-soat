<?php

declare(strict_types=1);

namespace App\Application\Cliente;

use App\Domain\Cliente\Entity;
use App\Domain\Usuario\ProfileEnum;

use App\Interface\Gateway\ClienteGateway;
use App\Interface\Gateway\UsuarioGateway;

use DateTime;
use RuntimeException;

use App\Exception\DomainHttpException;

final class CreateUseCase
{
    private ?ClienteGateway $gateway = null;
    private ?UsuarioGateway $usuarioGateway = null;

    public function __construct(
        public string $nome,
        public string $email,
        public string $documento,
        public string $fone,
    ) {}

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

    public function handle(string $authenticatedUserUuid): array
    {
        if ($this->gateway instanceof ClienteGateway === false) {
            throw new DomainHttpException('Gateway de cliente não definido', 500);
        }

        if ($this->usuarioGateway instanceof UsuarioGateway === false) {
            throw new DomainHttpException('Gateway de usuário não definido', 500);
        }

        if (empty(trim($authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        $authenticatedUser = $this->usuarioGateway->findOneBy('uuid', $authenticatedUserUuid);

        if ($authenticatedUser === null || (is_array($authenticatedUser) && sizeof($authenticatedUser) === 0) || (is_array($authenticatedUser) && !isset($authenticatedUser['uuid']))) {
            throw new DomainHttpException('O usuário autenticado com o identificador informadas não foi encontrado', 404);
        }

        if (! in_array($authenticatedUser['perfil'], [ProfileEnum::ADMIN->value, ProfileEnum::ATENDENTE->value])) {
            throw new DomainHttpException('Você não tem permissão para realizar essa ação. Somente um administrador ou atendente pode cadastrar um cliente', 404);
        }

        $usuarioComMesmoEmail = $this->gateway->findOneBy('email', $this->email);

        if (is_array($usuarioComMesmoEmail) && sizeof($usuarioComMesmoEmail) && isset($usuarioComMesmoEmail['uuid'])) {
            throw new DomainHttpException('Já existe um usuário com este e-mail', 400);
        }

        $entity = new Entity(
            '',
            $this->nome,
            $this->documento,
            $this->email,
            $this->fone,
            new DateTime(),
            null,
            null,
        );

        $usuarioComMesmoDoc = $this->gateway->findOneBy('documento', $entity->documento);

        if (! is_null($usuarioComMesmoDoc) && is_array($usuarioComMesmoDoc) && sizeof($usuarioComMesmoDoc) && isset($usuarioComMesmoDoc['uuid'])) {
            throw new DomainHttpException('Documento já cadastrado', 400);
        }

        $res = $this->gateway->create($entity->asArray());

        $entity->uuid = $res['uuid'];
        $entity->criadoEm = new DateTime($res['criado_em']);

        return $entity->toExternal();
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Usuario;

use App\Domain\Usuario\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\UsuarioGateway;
use DateTime;
use RuntimeException;
use \App\Exception\DomainHttpException;

final class CreateUnauthenticatedUseCase
{
    private ?UsuarioGateway $gateway = null;

    public function __construct(
        public string $nome,
        public string $email,
        public string $senhaAcessoSistema,
        public string $perfil,
        public bool $ativo,
    ) {}

    public function useGateway(UsuarioGateway $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function handle(): array
    {
        if ($this->gateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        if (!is_null($this->gateway->findOneBy('email', $this->email))) {
            throw new DomainHttpException('Já existe um usuário com este e-mail', 400);
        }

        $entity = new Entity(
            '',
            $this->nome,
            $this->email,
            password_hash($this->senhaAcessoSistema, PASSWORD_BCRYPT),
            $this->ativo,

            ProfileEnum::from($this->perfil),

            new DateTime()
        );

        $res = $this->gateway->create($entity->asArray());

        $entity->uuid = $res['uuid'];
        $entity->cadastradoEm = new DateTime($res['cadastrado_em']);

        return $entity->toExternal();
    }
}

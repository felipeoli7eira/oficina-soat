<?php

declare(strict_types=1);

namespace App\Application\Usuario;

use App\Domain\Usuario\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\UsuarioGateway;
use DateTime;
use RuntimeException;
use \App\Exception\DomainHttpException;

final class PasswordVerifyUseCase
{
    private ?UsuarioGateway $gateway = null;

    public function __construct(public readonly string $email, public readonly string $password) {}

    public function useGateway(UsuarioGateway $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function handle(): Entity
    {
        if ($this->gateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $rawData = $this->gateway->findOneBy('email', $this->email);

        if ($rawData === null || sizeof($rawData) === 0 || !isset($rawData['uuid'])) {
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

        $verified = $domainEntity->passwordVerify($this->password);

        if ($verified === false) {
            throw new DomainHttpException('Usuário não reconhecido com as credenciais informadas', 401);
        }

        return $domainEntity;
    }
}

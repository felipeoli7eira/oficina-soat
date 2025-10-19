<?php

declare(strict_types=1);

namespace App\Application\Usuario;

use App\Domain\Usuario\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\UsuarioGateway;
use DateTime;
use RuntimeException;
use App\Exception\DomainHttpException;

final class UpdateUseCase
{
    private ?UsuarioGateway $gateway = null;

    public function __construct(public string $uuid, public array $novosDados) {}

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

        $usuarioDoUuidInformado = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($usuarioDoUuidInformado === null) {
            throw new DomainHttpException('Usuário informado não encontrado', 404);
        }

        $entity = new Entity(
            $usuarioDoUuidInformado['uuid'],
            $usuarioDoUuidInformado['nome'],
            $usuarioDoUuidInformado['email'],
            $usuarioDoUuidInformado['senha'],
            $usuarioDoUuidInformado['ativo'],

            ProfileEnum::from($usuarioDoUuidInformado['perfil']),
            new DateTime($usuarioDoUuidInformado['cadastrado_em']),
            $usuarioDoUuidInformado['atualizado_em'] ? new DateTime($usuarioDoUuidInformado['atualizado_em']) : null,
            $usuarioDoUuidInformado['deletado_em'] ? new DateTime($usuarioDoUuidInformado['deletado_em']) : null,
        );

        $entity->update($this->novosDados);

        $dadosParaUpdate = $entity->asArray();

        unset($dadosParaUpdate['uuid']);

        $res = $this->gateway->update($this->uuid, $dadosParaUpdate);

        if ($res === 0) {
            throw new DomainHttpException('0 (zero) linhas afetadas no update', 500);
        }

        return $entity->toExternal();
    }
}

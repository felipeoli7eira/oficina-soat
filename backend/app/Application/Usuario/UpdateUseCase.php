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

    public function handle(string $authenticatedUserUuid): array
    {
        if (empty(trim($authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        if ($this->gateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        $authenticatedUser = $this->gateway->findOneBy('uuid', $authenticatedUserUuid);

        if ($authenticatedUser === null || (is_array($authenticatedUser) && sizeof($authenticatedUser) === 0) || (is_array($authenticatedUser) && !isset($authenticatedUser['uuid']))) {
            throw new DomainHttpException('O usuário autenticado com o identificador informadas não foi encontrado', 404);
        }

        $usuarioDoUuidInformado = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($usuarioDoUuidInformado === null || (is_array($usuarioDoUuidInformado) && sizeof($usuarioDoUuidInformado) === 0) || (is_array($usuarioDoUuidInformado) && !isset($usuarioDoUuidInformado['uuid']))) {
            throw new DomainHttpException('Usuário informado não encontrado', 404);
        }

        // Somente um admin pode atualizar dados de usuarios do sistema.
        $usuarioAutenticadoNaoEhAdmin = $authenticatedUser['perfil'] !== ProfileEnum::ADMIN->value;

        if ($authenticatedUserUuid !== $usuarioDoUuidInformado['uuid'] && $usuarioAutenticadoNaoEhAdmin) {
            throw new DomainHttpException('Permissão negada! Somente um admin pode atualizar os dados de outro usuário', 401);
        }

        // daqui pra baixo, eh um admin ou um usuario tentanto atualizar seus proprios dados

        // Se o usuario esta tentando atualizar seus proprios dados, tudo bem, mas nao pode atualizar seu perfil e nem seu status "ativo".

        if (isset($this->novosDados['email']) && $this->novosDados['email'] !== $usuarioDoUuidInformado['email'] && $this->gateway->findOneBy('email', $this->novosDados['email']) !== null) {
            throw new DomainHttpException('Já existe um usuário usando o e-mail informado', 400);
        }

        $entity = new Entity(
            $usuarioDoUuidInformado['uuid'],

            $usuarioDoUuidInformado['nome'],
            $usuarioDoUuidInformado['email'],
            $usuarioDoUuidInformado['senha'],
            $usuarioDoUuidInformado['ativo'],

            ProfileEnum::from($usuarioDoUuidInformado['perfil']),

            new DateTime($usuarioDoUuidInformado['criado_em']),
            new DateTime(),
        );

        if ($authenticatedUserUuid === $usuarioDoUuidInformado['uuid']) {
            $entity->update([
                'nome' => isset($this->novosDados['nome']) ? $this->novosDados['nome'] : $usuarioDoUuidInformado['nome'],
                'email' => isset($this->novosDados['email']) ? $this->novosDados['email'] : $usuarioDoUuidInformado['email'],
                'senha' => isset($this->novosDados['senha']) ? password_hash($this->novosDados['senha'], PASSWORD_BCRYPT) : $usuarioDoUuidInformado['senha'],
            ]);
        }

        if ($authenticatedUserUuid !== $usuarioDoUuidInformado['uuid'] && $authenticatedUser['perfil'] === ProfileEnum::ADMIN->value) {
            $entity->update([
                'nome' => isset($this->novosDados['nome']) ? $this->novosDados['nome'] : $usuarioDoUuidInformado['nome'],
                'email' => isset($this->novosDados['email']) ? $this->novosDados['email'] : $usuarioDoUuidInformado['email'],
                'senha' => isset($this->novosDados['senha']) ? $this->novosDados['senha'] : $usuarioDoUuidInformado['senha'],
                'ativo' => isset($this->novosDados['ativo']) ? $this->novosDados['ativo'] : $usuarioDoUuidInformado['ativo'],
                'perfil' => isset($this->novosDados['perfil']) ? $this->novosDados['perfil'] : $usuarioDoUuidInformado['perfil'],
            ]);
        }

        $dadosParaUpdate = $entity->asArray();

        unset($dadosParaUpdate['uuid']);

        $res = $this->gateway->update($this->uuid, $dadosParaUpdate);

        if ($res === 0) {
            throw new DomainHttpException('0 (zero) linhas afetadas no update', 500);
        }

        return $entity->toExternal();
    }
}

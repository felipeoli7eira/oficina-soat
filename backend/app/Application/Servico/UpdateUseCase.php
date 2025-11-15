<?php

declare(strict_types=1);

namespace App\Application\Cliente;

use App\Domain\Cliente\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\ClienteGateway;
use DateTime;
use RuntimeException;
use App\Exception\DomainHttpException;
use App\Interface\Gateway\UsuarioGateway;

final class UpdateUseCase
{
    private ?ClienteGateway $gateway = null;
    private ?UsuarioGateway $usuarioGateway = null;

    public function __construct(public string $uuid, public array $novosDados) {}

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

        if ($authenticatedUser === null || (is_array($authenticatedUser) && sizeof($authenticatedUser) === 0) || (is_array($authenticatedUser) && !isset($authenticatedUser['uuid']))) {
            throw new DomainHttpException('O usuário autenticado com o identificador informadas não foi encontrado', 404);
        }

        $clienteParaAtualizacao = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($clienteParaAtualizacao === null || (is_array($clienteParaAtualizacao) && sizeof($clienteParaAtualizacao) === 0) || (is_array($clienteParaAtualizacao) && !isset($clienteParaAtualizacao['uuid']))) {
            throw new DomainHttpException('Cliente informado não encontrado', 404);
        }

        // Somente um admin ou atendente pode atualizar dados de clientes.

        if (! in_array($authenticatedUser['perfil'], [ProfileEnum::ADMIN->value, ProfileEnum::ATENDENTE->value])) {
            throw new DomainHttpException('Permissão negada! Somente um admin ou atendente pode atualizar os dados de clientes', 401);
        }

        $entity = new Entity(
            $clienteParaAtualizacao['uuid'],

            $clienteParaAtualizacao['nome'],
            $clienteParaAtualizacao['documento'],
            $clienteParaAtualizacao['email'],
            $clienteParaAtualizacao['fone'],

            new DateTime($clienteParaAtualizacao['criado_em']),
            new DateTime(),
        );

        if ($existeOutroComDocInformado = $this->gateway->findOneBy('documento', $entity->documento, ['excludeEqual' => [['uuid', $this->uuid]]])) {
            throw new DomainHttpException('Documento já sendo usado por outro cliente', 400);
        }

        if ($existeOutroComMesmoEmail = $this->gateway->findOneBy('email', $entity->email, ['excludeEqual' => [['uuid', $this->uuid]]])) {
            throw new DomainHttpException('E-mail já sendo usado por outro cliente', 400);
        }

        if ($existeOutroComMesmoFone = $this->gateway->findOneBy('fone', $entity->fone, ['excludeEqual' => [['uuid', $this->uuid]]])) {
            throw new DomainHttpException('Fone já sendo usado por outro cliente', 400);
        }

        $entity->update([
            'nome'      => isset($this->novosDados['nome'])      ? $this->novosDados['nome']      : $clienteParaAtualizacao['nome'],
            'email'     => isset($this->novosDados['email'])     ? $this->novosDados['email']     : $clienteParaAtualizacao['email'],
            'documento' => isset($this->novosDados['documento']) ? $this->novosDados['documento'] : $clienteParaAtualizacao['documento'],
            'fone'      => isset($this->novosDados['fone'])      ? $this->novosDados['fone']      : $clienteParaAtualizacao['fone'],
        ]);

        $dadosParaUpdate = $entity->asArray();

        unset($dadosParaUpdate['uuid']);

        $res = $this->gateway->update($this->uuid, $dadosParaUpdate);

        if ($res === 0) {
            throw new DomainHttpException('0 (zero) linhas afetadas no update', 500);
        }

        return $entity->toExternal();
    }
}

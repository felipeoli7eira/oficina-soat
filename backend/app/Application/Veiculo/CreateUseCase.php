<?php

declare(strict_types=1);

namespace App\Application\Veiculo;

use App\Domain\Veiculo\Entity;
use App\Domain\Usuario\ProfileEnum;

use App\Interface\Gateway\VeiculoGateway;
use App\Interface\Gateway\UsuarioGateway;
use App\Interface\Gateway\ClienteGateway;

use DateTime;

use App\Exception\DomainHttpException;

final class CreateUseCase
{
    private readonly VeiculoGateway $gateway;
    private readonly UsuarioGateway $usuarioGateway;
    private readonly ClienteGateway $clienteGateway;

    public function __construct(
        public string $marca,
        public string $modelo,
        public string $placa,
        public int $ano,
        public string $clienteDonoUuid,
    ) {}

    public function useGateway(VeiculoGateway $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function useUsuarioGateway(UsuarioGateway $gateway): self
    {
        $this->usuarioGateway = $gateway;
        return $this;
    }

    public function useClienteGateway(ClienteGateway $gateway): self
    {
        $this->clienteGateway = $gateway;
        return $this;
    }

    public function handle(string $authenticatedUserUuid): array
    {
        if ($this->gateway instanceof VeiculoGateway === false) {
            throw new DomainHttpException('Gateway de veiculo não definido', 500);
        }

        if ($this->usuarioGateway instanceof UsuarioGateway === false) {
            throw new DomainHttpException('Gateway de usuário não definido', 500);
        }

        if ($this->clienteGateway instanceof ClienteGateway === false) {
            throw new DomainHttpException('Gateway de cliente não definido', 500);
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

        $cliente = $this->clienteGateway->findOneBy('uuid', $this->clienteDonoUuid);

        if ($cliente === null || (is_array($cliente) && count($cliente) === 0) || (is_array($cliente) && !isset($cliente['uuid']))) {
            throw new DomainHttpException('O cliente informado não foi encontrado', 404);
        }

        $entity = new Entity(
            '',
            $this->marca,
            $this->modelo,
            $this->placa,
            $this->ano,
            $this->clienteDonoUuid,
            new DateTime(),
            null,
            null
        );

        $veiculoComMesmaPlaca = $this->gateway->findOneBy('placa', $entity->placa);

        if (! is_null($veiculoComMesmaPlaca) && is_array($veiculoComMesmaPlaca) && sizeof($veiculoComMesmaPlaca) && isset($veiculoComMesmaPlaca['uuid'])) {
            throw new DomainHttpException('Veículo com a placa informada já cadastrado', 400);
        }

        $res = $this->gateway->create($entity->asArray());

        $entity->uuid = $res['uuid'];
        $entity->criadoEm = new DateTime($res['criado_em']);

        return $entity->toExternal();
    }
}

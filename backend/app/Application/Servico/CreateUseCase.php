<?php

declare(strict_types=1);

namespace App\Application\Servico;

use App\Domain\Servico\Entity;
use App\Domain\Usuario\ProfileEnum;

use App\Interface\Gateway\ServicoGateway;
use App\Interface\Gateway\UsuarioGateway;

use DateTime;
use RuntimeException;

use App\Exception\DomainHttpException;

final class CreateUseCase
{
    private ?ServicoGateway $gateway = null;
    private ?UsuarioGateway $usuarioGateway = null;

    public function __construct(
        public string $nome,
        public float $valor,
        public bool $statusDisponivel = false
    ) {}

    public function useGateway(ServicoGateway $gateway): self
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
        if ($this->gateway instanceof ServicoGateway === false) {
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

        $servicoComMesmoNome = $this->gateway->findOneBy('nome', $this->nome);

        if (is_array($servicoComMesmoNome) && isset($servicoComMesmoNome['uuid'])) {
            throw new DomainHttpException('Já existe um serviço cadastrado com este nome: ' . $servicoComMesmoNome['nome'], 400);
        }

        $entity = new Entity(
            '',
            $this->nome,
            $this->valor,
            $this->statusDisponivel,

            new DateTime(),
            null,
            null,
        );

        $entity->converteValorEmCentavos();

        $res = $this->gateway->create($entity->asArray());

        $entity->uuid = $res['uuid'];
        $entity->criadoEm = new DateTime($res['criado_em']);

        return $entity->toExternal();
    }
}

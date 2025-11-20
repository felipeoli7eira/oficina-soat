<?php

declare(strict_types=1);

namespace App\Application\Servico;

use App\Domain\Servico\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\ServicoGateway;
use DateTime;
use RuntimeException;
use App\Exception\DomainHttpException;
use App\Interface\Gateway\UsuarioGateway;

final class UpdateUseCase
{
    private ?ServicoGateway $gateway = null;
    private ?UsuarioGateway $usuarioGateway = null;

    public function __construct(public string $uuid, public array $novosDados) {}

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
        if (empty(trim($authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        if ($this->gateway instanceof ServicoGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        if ($this->usuarioGateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway de usuário não definido');
        }

        $authenticatedUser = $this->usuarioGateway->findOneBy('uuid', $authenticatedUserUuid);

        if ($authenticatedUser === null || (is_array($authenticatedUser) && sizeof($authenticatedUser) === 0) || (is_array($authenticatedUser) && !isset($authenticatedUser['uuid']))) {
            throw new DomainHttpException('O usuário autenticado com o identificador informadas não foi encontrado', 404);
        }

        $servicoParaAtualizacao = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($servicoParaAtualizacao === null || (is_array($servicoParaAtualizacao) && sizeof($servicoParaAtualizacao) === 0) || (is_array($servicoParaAtualizacao) && !isset($servicoParaAtualizacao['uuid']))) {
            throw new DomainHttpException('Servico informado não encontrado', 404);
        }

        // Somente um admin ou atendente pode atualizar os dados

        if (! in_array($authenticatedUser['perfil'], [ProfileEnum::ADMIN->value, ProfileEnum::ATENDENTE->value])) {
            throw new DomainHttpException('Permissão negada! Somente um admin ou atendente pode atualizar os dados', 401);
        }

        $entity = new Entity(
            $servicoParaAtualizacao['uuid'],

            $servicoParaAtualizacao['nome'],
            $servicoParaAtualizacao['valor'],
            $servicoParaAtualizacao['disponivel'],

            new DateTime($servicoParaAtualizacao['criado_em']),
            new DateTime(),
        );

        if ($existeOutroComMesmoNome = $this->gateway->findOneBy('nome', $entity->nome, ['excludeEqual' => [['uuid', $this->uuid]]])) {
            throw new DomainHttpException('Já existe um serviço com esse nome', 400);
        }

        $entity->update($this->novosDados);

        if (array_key_exists('valor', $this->novosDados)) {
            $entity->converteValorEmCentavos();
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

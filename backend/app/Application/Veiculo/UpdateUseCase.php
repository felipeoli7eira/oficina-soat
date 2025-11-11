<?php

declare(strict_types=1);

namespace App\Application\Veiculo;

use App\Domain\Veiculo\Entity;
use App\Domain\Usuario\ProfileEnum;
use App\Interface\Gateway\ClienteGateway;
use DateTime;
use RuntimeException;
use App\Exception\DomainHttpException;
use App\Interface\Gateway\VeiculoGateway;
use App\Interface\Gateway\UsuarioGateway;

final class UpdateUseCase
{
    private ?VeiculoGateway $gateway = null;
    private ?UsuarioGateway $usuarioGateway = null;

    public function __construct(public string $uuid, public array $novosDados) {}

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

    public function handle(string $authenticatedUserUuid): array
    {
        if (empty(trim($authenticatedUserUuid))) {
            throw new DomainHttpException('É necessário identificação para realizar esse procedimento', 401);
        }

        if ($this->gateway instanceof VeiculoGateway === false) {
            throw new RuntimeException('Gateway não definido');
        }

        if ($this->usuarioGateway instanceof UsuarioGateway === false) {
            throw new RuntimeException('Gateway de usuário não definido');
        }

        $authenticatedUser = $this->usuarioGateway->findOneBy('uuid', $authenticatedUserUuid);

        if ($authenticatedUser === null || (is_array($authenticatedUser) && sizeof($authenticatedUser) === 0) || (is_array($authenticatedUser) && !isset($authenticatedUser['uuid']))) {
            throw new DomainHttpException('O usuário autenticado com o identificador informadas não foi encontrado', 404);
        }

        $veiculoParaAtualizacao = $this->gateway->findOneBy('uuid', $this->uuid);

        if ($veiculoParaAtualizacao === null || (is_array($veiculoParaAtualizacao) && sizeof($veiculoParaAtualizacao) === 0) || (is_array($veiculoParaAtualizacao) && !isset($veiculoParaAtualizacao['uuid']))) {
            throw new DomainHttpException('Veículo com o identificador informado não foi encontrado', 404);
        }

        // Somente um admin ou atendente pode atualizar dados de veiculo

        if (! in_array($authenticatedUser['perfil'], [ProfileEnum::ADMIN->value, ProfileEnum::ATENDENTE->value])) {
            throw new DomainHttpException('Permissão negada! Somente um admin ou atendente pode atualizar essas informações.', 401);
        }

        $entity = new Entity(
            $veiculoParaAtualizacao['uuid'],

            $veiculoParaAtualizacao['marca'],
            $veiculoParaAtualizacao['modelo'],
            $veiculoParaAtualizacao['placa'],
            $veiculoParaAtualizacao['ano'],
            $veiculoParaAtualizacao['cliente_uuid'],

            new DateTime($veiculoParaAtualizacao['criado_em']),
            new DateTime(),
        );

        if ($existeOutroComPlacaInformado = $this->gateway->findOneBy('placa', $entity->placa, ['excludeEqual' => [['uuid', $this->uuid]]])) {
            throw new DomainHttpException('Placa já sendo usada por outro veículo', 400);
        }

        $entity->update([
            'ano'    => isset($this->novosDados['ano'])     ? $this->novosDados['ano']     : $veiculoParaAtualizacao['ano'],
            'modelo' => isset($this->novosDados['modelo'])  ? $this->novosDados['modelo']  : $veiculoParaAtualizacao['modelo'],
            'placa'  => isset($this->novosDados['placa'])   ? $this->novosDados['placa']   : $veiculoParaAtualizacao['placa'],
            'marca'  => isset($this->novosDados['marca'])   ? $this->novosDados['marca']   : $veiculoParaAtualizacao['marca'],
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

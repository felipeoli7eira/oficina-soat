<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Service;

use App\Modules\Veiculo\Dto\AtualizacaoDto;
use App\Modules\Veiculo\Dto\CadastroDto;
use App\Modules\Veiculo\Dto\ListagemDto;
use App\Modules\Veiculo\Repository\VeiculoRepository;
use App\Modules\ClienteVeiculo\Service\Service as ClienteVeiculoService;
use App\Modules\ClienteVeiculo\Dto\CadastroDto as ClienteVeiculoCadastroDto;
use App\Modules\Cliente\Service\Service as ClienteService;

class Service
{
    public function __construct(
        private readonly VeiculoRepository $repo,
        private readonly ClienteVeiculoService $clienteVeiculoService,
        private readonly ClienteService $clienteService
    ) {}

    public function listagem(ListagemDto $dto)
    {
        return $this->repo->read();
    }

    public function cadastro(CadastroDto $dto)
    {
        $veiculo = $this->repo->createOrFirst($dto->asArray())->fresh();

        $this->anexarCliente($dto->clienteUuid, $veiculo->id);

        return $veiculo;
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $dados = $dto->dados;
        unset($dados['uuid']);

        if (empty($dados)) {
            return [];
        }

        $clienteUuid = null;
        if (isset($dados['cliente_uuid'])) {
            $clienteUuid = $dados['cliente_uuid'];
            unset($dados['cliente_uuid']);
        }

        $veiculo = $this->obterUmPorUuid($uuid);
        $atualizacao = $dto->merge($veiculo->toArray());
        $veiculo->update($atualizacao);

        $this->anexarCliente($clienteUuid, $veiculo->id);
        return $veiculo->refresh();
    }

    public function anexarCliente(?string $clienteUuid, int $veiculoId): void
    {
        if (empty($clienteUuid)) return;

        $cliente = $this->clienteService->obterUmPorUuid($clienteUuid);

        $clienteVeiculoDto = new ClienteVeiculoCadastroDto(
            veiculoId: $veiculoId,
            clienteId: $cliente->id
        );

        $this->clienteVeiculoService->cadastro($clienteVeiculoDto);
    }
}

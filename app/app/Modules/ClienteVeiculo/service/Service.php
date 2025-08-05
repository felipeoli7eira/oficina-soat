<?php

declare(strict_types=1);

namespace App\Modules\ClienteVeiculo\Service;

use App\Modules\ClienteVeiculo\Dto\AtualizacaoDto;
use App\Modules\ClienteVeiculo\Dto\CadastroDto;
use App\Modules\ClienteVeiculo\Dto\ListagemDto;
use App\Modules\ClienteVeiculo\Repository\Repository;

class Service
{
    public function __construct(private readonly Repository $repo) {}

    public function listagem(ListagemDto $dto)
    {
        return $this->repo->read();
    }

    public function cadastro(CadastroDto $dto)
    {
        // Verificar se já existe o relacionamento
        $clienteVeiculoExistente = $this->obterUmPorClienteEVeiculo($dto->clienteId, $dto->veiculoId);
        
        if ($clienteVeiculoExistente) {
            return $clienteVeiculoExistente;
        }

        return $this->repo->create($dto->asArray());
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
        $dados = $dto->asArray();
        unset($dados['uuid']);

        if (empty($dados)) {
            return [];
        }

        $clienteVeiculo = $this->obterUmPorUuid($uuid);

        $atualizacao = $dto->merge($clienteVeiculo->toArray());

        $clienteVeiculo->update($atualizacao);

        return $clienteVeiculo->refresh();
    }

    public function obterUmPorClienteEVeiculo(int $clienteId, int $veiculoId)
    {
        return $this->repo->model()
            ->where('cliente_id', $clienteId)
            ->where('veiculo_id', $veiculoId)
            ->first();
    }
}

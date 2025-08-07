<?php

declare(strict_types=1);

namespace App\Modules\ClienteVeiculo\Service;

use App\Modules\ClienteVeiculo\Dto\AtualizacaoDto;
use App\Modules\ClienteVeiculo\Dto\CadastroDto;
use App\Modules\ClienteVeiculo\Dto\ListagemDto;
use App\Modules\ClienteVeiculo\Repository\Repository;
use App\Modules\Veiculo\Service\Service as VeiculoService;
use App\Modules\Cliente\Service\Service as ClienteService;

class Service
{
    public function __construct(
        private readonly Repository $repo,
        private readonly ClienteService $clienteService
    ) {}

    public function cadastro(CadastroDto $dto)
    {
        $clienteVeiculoExistente = $this->obterUmPorClienteEVeiculo($dto->clienteId, $dto->veiculoId);

        if ($clienteVeiculoExistente) {
            return $clienteVeiculoExistente;
        }

        return $this->repo->createOrFirst($dto->asArray())->fresh();
    }

    public function obterUmPorClienteEVeiculo(int $clienteId, int $veiculoId)
    {
        return $this->repo->model()
            ->where('cliente_id', $clienteId)
            ->where('veiculo_id', $veiculoId)
            ->first();
    }
}

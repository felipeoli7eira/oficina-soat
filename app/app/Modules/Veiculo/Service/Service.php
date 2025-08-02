<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Service;

use App\Modules\Veiculo\Dto\AtualizacaoDto;
use App\Modules\Veiculo\Dto\CadastroDto;
use App\Modules\Veiculo\Dto\ListagemDto;
use App\Modules\Veiculo\Repository\VeiculoRepository;

class Service
{
    public function __construct(private readonly VeiculoRepository $repo) {}

    public function listagem(ListagemDto $dto)
    {
        return $this->repo->read();
    }

    public function cadastro(CadastroDto $dto)
    {
        return $this->repo->createOrFirst($dto->asArray())->fresh();
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

        $veiculo = $this->obterUmPorUuid($uuid);

        $atualizacao = $dto->merge($veiculo->toArray());

        $veiculo->update($atualizacao);

        return $veiculo->refresh();
    }
}

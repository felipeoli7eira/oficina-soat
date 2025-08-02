<?php

declare(strict_types=1);

namespace App\Modules\PecaInsumo\Service;

use App\Modules\PecaInsumo\Dto\AtualizacaoDto;
use App\Modules\PecaInsumo\Dto\CadastroDto;
use App\Modules\PecaInsumo\Dto\ListagemDto;
use App\Modules\PecaInsumo\Repository\PecaInsumoRepository;

class Service
{
    public function __construct(private readonly PecaInsumoRepository $repo) {}

    public function listagem(ListagemDto $dto)
    {
        return $this->repo->read();
    }

    public function cadastro(CadastroDto $dto)
    {
        return $this->repo->createOrFirst($dto->asArray())->fresh();
    }

    public function obterUmPorId(string $id)
    {
        return $this->repo->model()->where('id', $id)->firstOrFail();
    }

    public function remocao(string $id)
    {
        return $this->obterUmPorId($id)->delete();
    }

    public function atualizacao(string $id, AtualizacaoDto $dto)
    {
        $dados = $dto->dados;
        unset($dados['id']);

        if (empty($dados)) {
            return [];
        }

        $pecaInsumo = $this->obterUmPorId($id);

        $atualizacao = $dto->merge($pecaInsumo->toArray());

        $pecaInsumo->update($atualizacao);

        return $pecaInsumo->refresh();
    }
}

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
        $pecaInsumo = $this->repo->findOne((int)$id);
        if (!$pecaInsumo) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("PecaInsumo with ID {$id} not found.");
        }
        return $pecaInsumo;
    }

    public function remocao(string $id)
    {
        $pecaInsumo = $this->obterUmPorId($id);
        return $pecaInsumo->delete();
    }

    public function atualizacao(string $id, AtualizacaoDto $dto)
    {
        $dados = $dto->asArray();
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

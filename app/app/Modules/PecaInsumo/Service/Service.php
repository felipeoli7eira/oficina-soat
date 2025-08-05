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

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        dd('Remoção lógica de PecaInsumo por UUID: ' . $uuid);
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        dd('Atualização de PecaInsumo por UUID: ' . $uuid);

        $dados = $dto->asArray();
        unset($dados['uuid']);

        if (empty($dados)) {
            return [];
        }

        $pecaInsumo = $this->obterUmPorUuid($uuid);
        $atualizacao = $dto->merge($pecaInsumo->toArray());

        $pecaInsumo->update($atualizacao);

        return $pecaInsumo->refresh();
    }
}

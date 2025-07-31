<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Service;

use App\Modules\Usuario\Dto\AtualizacaoDto;
use App\Modules\Usuario\Dto\CadastroDto;
use App\Modules\Usuario\Dto\ListagemDto;
use App\Modules\Usuario\Repository\UsuarioRepository;

class Service
{
    public function __construct(private readonly UsuarioRepository $repo) {}

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
        $dados = $dto->asArray();
        unset($dados['uuid']);

        if (empty($dados)) {
            return [];
        }

        $cliente = $this->obterUmPorUuid($uuid);

        $atualizacao = $dto->merge($cliente->toArray());

        $cliente->update($atualizacao);

        return $cliente->refresh();
    }
}

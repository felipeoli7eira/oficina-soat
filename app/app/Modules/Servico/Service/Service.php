<?php

declare(strict_types=1);

namespace App\Modules\Servico\Service;

use App\Modules\Servico\Dto\AtualizacaoDto;
use App\Modules\Servico\Dto\CadastroDto;
use App\Modules\Servico\Dto\ListagemDto;
use App\Modules\Servico\Repository\ServicoRepository;

class Service
{
    public function __construct(private readonly ServicoRepository $repo) {}

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
        return $this->repo->model()
                          ->where('uuid', $uuid)
                          ->firstOrFail();
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

        $servico = $this->obterUmPorUuid($uuid);

        $atualizacao = $dto->merge($servico->toArray());

        $servico->update($atualizacao);

        return $servico->refresh();
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\OS\Service;

use App\Modules\OS\Dto\AtualizacaoDto;
use App\Modules\OS\Dto\CadastroDto;

use App\Modules\OS\Repository\Repository as OSRepository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Service
{
    public function __construct(private readonly OSRepository $repo) {}

    public function listagem(): ResourceCollection|LengthAwarePaginator
    {
        return $this->repo->read();
    }

    public function cadastro(CadastroDto $dto)
    {
        return $this->repo->createOrFirst($dto->asArray())->fresh(['role']);
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->with(['role'])->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $usuario = $this->obterUmPorUuid($uuid);

        $novosDados = $dto->merge($usuario->toArray());

        $usuario->update($novosDados);

        return $usuario->refresh();
    }
}

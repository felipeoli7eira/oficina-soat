<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Service;

use App\Modules\Usuario\Dto\AtualizacaoDto;
use App\Modules\Usuario\Dto\CadastroDto;
use App\Modules\Usuario\Repository\UsuarioRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Service
{
    public function __construct(private readonly UsuarioRepository $repo) {}

    public function listagem(): ResourceCollection|LengthAwarePaginator
    {
        return $this->repo->read();
    }

    public function cadastro(CadastroDto $dto)
    {
        $data = $dto->asArray();
        $role = $data['role'];

        unset($data['role']);

        $usuario = $this->repo->createOrFirst($data);

        if (! $usuario->hasRole($role)) {
            $usuario->assignRole($role);
        }

        return $usuario->fresh();
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->with(['roles'])->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $usuario = $this->obterUmPorUuid($uuid);

        $dadosAntigos = $usuario->toArray();
        $novosDados = $dto->merge($dadosAntigos);

        $usuario->update($novosDados);

        if ($novoPapel = $novosDados['role'] ?? null) {
            $usuario->syncRoles($novoPapel);
        }

        return $usuario->fresh(['roles']);
    }

    public function login(string $email, string $senha)
    {
        return [
            $email,
            $senha,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Str;
use App\Models\UsuarioModel;
use App\Domain\Usuario\Entidade;
use App\Infrastructure\Dto\UsuarioDto;
use Illuminate\Support\Facades\Hash;
use App\Domain\Usuario\RepositorioInterface;
use App\Domain\Usuario\Mapper as UsuarioMapper;

class UsuarioEloquentRepository implements RepositorioInterface
{
    public function __construct(private readonly UsuarioModel $model) {}

    public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    {
        $modelResult = $this->model->query()->where($nomeIdentificador, $identificador);

        if ($modelResult->exists()) {
            $modelValue = $modelResult->first();

            return (new UsuarioMapper())->fromModelToEntity($modelValue);
        }

        return null;
    }

    public function criar(UsuarioDto $dados): Entidade
    {
        $model = $this->model->query()->create([
            'uuid'      => Str::uuid()->toString(),
            'nome'      => $dados->nome,
            'email'     => $dados->email,
            'senha'     => Hash::make($dados->senha),
            'ativo'     => Entidade::STATUS_ATIVO,
        ]);

        return (new UsuarioMapper())->fromModelToEntity(
            $model->refresh()
        );
    }

    public function listar(array $columns = ['*']): array
    {
        return $this->model->query()->get($columns)->toArray();
    }

    public function deletar(string $uuid): bool
    {
        $del = $this->model->query()->where('uuid', $uuid)->delete();

        if (! $del) {
            return false;
        }

        return true;
    }

    public function atualizar(UsuarioDto $dados): Entidade
    {
        $model = $this->model->query()->where('uuid', $dados->uuid)->first();

        $model->update([
            'nome' => $dados->nome,
        ]);

        return (new UsuarioMapper())->fromModelToEntity(
            $model->refresh()
        );
    }
}

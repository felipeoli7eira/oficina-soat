<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Str;
use App\Models\UsuarioModel;
use App\Domain\Entity\Usuario\Entidade;
use App\Infrastructure\Dto\UsuarioDto;
use Illuminate\Support\Facades\Hash;
use App\Domain\Entity\Usuario\RepositorioInterface;
use App\Domain\Entity\Usuario\Mapper as UsuarioMapper;

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

    public function criar(array $dados): array
    {
        $model = $this->model->query()->create([
            'uuid'      => Str::uuid()->toString(),
            'nome'      => $dados['nome'],
            'email'     => $dados['email'],
            'senha'     => Hash::make($dados['senha']),
            'ativo'     => true,
            'perfil'    => $dados['perfil']
        ]);

        return $model->refresh()->toArray();
    }

    public function listar(array $columns = ['*']): array
    {
        return $this->model
            ->query()
            ->where('ativo', Entidade::STATUS_ATIVO)
            ->where('deletado_em', null)
            ->get($columns)
            ->toArray();
    }

    public function deletar(string $uuid): bool
    {
        $del = $this->model->query()->where('uuid', $uuid)->delete();

        if (! $del) {
            return false;
        }

        return true;
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        $model = $this->model->query()->where('uuid', $uuid)->first();

        $model->update([
            'nome' => $novosDados['nome'],
        ]);

        return $model->refresh()->toArray();
    }
}

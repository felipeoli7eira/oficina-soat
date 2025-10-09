<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Str;
use App\Models\MaterialModel;
use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Domain\Entity\Material\Mapper;

class MaterialEloquentRepository implements RepositorioInterface
{
    public function __construct(private readonly MaterialModel $model) {}

    public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    {
        $modelResult = $this->model->query()->where($nomeIdentificador, $identificador);

        if ($modelResult->exists()) {
            $modelValue = $modelResult->first();

            return (new Mapper())->fromModelToEntity($modelValue);
        }

        return null;
    }

    public function criar(array $dados): array
    {
        $model = $this->model->query()->create([
            ...$dados,

            'uuid' => Str::uuid()->toString(),
        ]);

        return $model->refresh()->toArray();
    }

    public function listar(array $columns = ['*']): array
    {
        return $this->model
            ->query()
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

        $model->update($novosDados);

        return $model->refresh()->toArray();
    }
}

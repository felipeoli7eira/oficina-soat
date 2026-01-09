<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Str;
use App\Models\ClienteModel;
use App\Domain\Entity\Cliente\Entidade;
use App\Domain\Entity\Cliente\RepositorioInterface;
use App\Domain\Entity\Cliente\Mapper;

class ClienteEloquentRepository implements RepositorioInterface
{
    public function __construct(public readonly ClienteModel $model) {}

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

    /**
     * Metodo responsavel por devolver um id numero, caso haja, a partir de um uuid
     *
     * @param string $uuid o uuid para ser resolvido em id numerico
     * @return int -1 para erro ou não encontrado, 1+ com o id encontrado
     */
    public function obterIdNumerico(string $uuid): int
    {
        $modelResult = $this->model->query()->where('uuid', $uuid);

        if (! $modelResult->exists()) {
            return -1;
        }

        $modelValue = $modelResult->first();

        if (! $modelValue->id) {
            return -1;
        }

        return $modelValue->id;
    }

    public function validaStatus(string $documento): bool
    {
        $modelResult = $this->model->query()->where('documento', $documento)->whereNull('deletado_em');

        return $modelResult->exists();
    }
}

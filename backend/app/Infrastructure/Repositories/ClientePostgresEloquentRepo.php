<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Service\UuidGenerator\UuidGeneratorContract;
use App\Models\ClienteModel as Model;
use DateTime;

class ClientePostgresEloquentRepo implements \App\Domain\Cliente\RepositoryContract
{
    public function __construct(public readonly UuidGeneratorContract $uuidGenerator) {}

    public function read(array $readParamsAndFilters = []): array
    {
        // TODO: implementar paginacao

        $where = [
            ['deletado_em', '=', null]
        ];

        $mod = Model::query()->where($where)->get();

        return $mod->toArray();
    }

    public function create(array $data): array
    {
        if (!isset($data['uuid']) || empty(trim($data['uuid']))) {
            $data['uuid'] = $this->uuidGenerator->generate();
        }

        $mod = Model::query()->create($data);

        return $mod->refresh()->toArray();
    }

    public function findOneBy(string $identifierName, mixed $value): ?array
    {
        $mod = Model::query()->where($identifierName, $value)->first();

        if (is_null($mod)) {
            return null;
        }

        if ($mod->exists()) {
            return $mod->toArray();
        }

        return null;
    }

    public function delete(array $deletedData, bool $soft = true): bool
    {
        $mod = Model::query()->where('uuid', $deletedData['uuid'])->first();

        if (is_null($mod)) {
            return false;
        }

        if ($soft) {
            $mod->update([
                'ativo'         => false,
                'atualizado_em' => (new DateTime())->format('Y-m-d H:i:s'),
                'deletado_em'   => (new DateTime())->format('Y-m-d H:i:s'),
            ]);
        } else {
            $mod->delete();
        }

        return true;
    }

    public function update(string $uuid, array $novosDados): int
    {
        $mod = Model::query()->where('uuid', $uuid)->first();

        if (is_null($mod)) {
            return 0;
        }

        $mod->update($novosDados);

        return $mod->count();
    }

    // public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    // {
    //     $modelResult = $this->model->query()->where($nomeIdentificador, $identificador);

    //     if ($modelResult->exists()) {
    //         $modelValue = $modelResult->first();

    //         return (new UsuarioMapper())->fromModelToEntity($modelValue);
    //     }

    //     return null;
    // }

    // public function criar(array $dados): array
    // {
    //     $model = $this->model->query()->create([
    //         'uuid'      => Str::uuid()->toString(),
    //         'nome'      => $dados['nome'],
    //         'email'     => $dados['email'],
    //         'senha'     => Hash::make($dados['senha']),
    //         'ativo'     => true,
    //         'perfil'    => $dados['perfil']
    //     ]);

    //     return $model->refresh()->toArray();
    // }

    // public function listar(array $columns = ['*']): array
    // {
    //     return $this->model
    //         ->query()
    //         ->where('ativo', Entidade::STATUS_ATIVO)
    //         ->where('deletado_em', null)
    //         ->get($columns)
    //         ->toArray();
    // }

    // public function deletar(string $uuid): bool
    // {
    //     $del = $this->model->query()->where('uuid', $uuid)->delete();

    //     if (! $del) {
    //         return false;
    //     }

    //     return true;
    // }

    // public function atualizar(string $uuid, array $novosDados): array
    // {
    //     $model = $this->model->query()->where('uuid', $uuid)->first();

    //     $model->update([
    //         'nome' => $novosDados['nome'],
    //     ]);

    //     return $model->refresh()->toArray();
    // }
}

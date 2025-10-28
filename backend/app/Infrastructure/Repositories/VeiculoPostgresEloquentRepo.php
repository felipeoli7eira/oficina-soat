<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Infrastructure\Service\UuidGenerator\UuidGeneratorContract;
use App\Models\VeiculoModel as Model;
use DateTime;

class VeiculoPostgresEloquentRepo implements \App\Domain\Veiculo\RepositoryContract
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

    public function findOneBy(string $identifierName, mixed $value, ?array $where = []): ?array
    {
        $mod = Model::query()->where($identifierName, $value);

        $mod = $mod->first();

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
}

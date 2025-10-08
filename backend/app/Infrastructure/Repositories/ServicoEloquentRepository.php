<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use Illuminate\Support\Str;
use App\Models\ServicoModel;
use App\Domain\Entity\Servico\Entidade;
use App\Infrastructure\Dto\UsuarioDto;
use Illuminate\Support\Facades\Hash;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Domain\Entity\Servico\Mapper as ServicoMapper;

class ServicoEloquentRepository implements RepositorioInterface
{
    public function __construct(private readonly ServicoModel $model) {}

    public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    {
        $modelResult = $this->model->query()->where($nomeIdentificador, $identificador);

        if ($modelResult->exists()) {
            $modelValue = $modelResult->first();

            return (new ServicoMapper())->fromModelToEntity($modelValue);
        }

        return null;
    }

    public function criar(array $dados): array
    {
        $model = $this->model->query()->create([
            'uuid'      => Str::uuid()->toString(),
            'nome'      => $dados['nome'],
            'valor'     => $dados['valor'],
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

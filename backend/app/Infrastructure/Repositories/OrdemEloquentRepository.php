<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Models\ClienteModel;
use App\Models\OrdemModel;
use App\Models\VeiculoModel;

use Illuminate\Support\Str;
use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Ordem\RepositorioInterface;
use App\Domain\Entity\Ordem\Mapper;
use App\Exception\DomainHttpException;

class OrdemEloquentRepository implements RepositorioInterface
{
    public function __construct(
        public readonly OrdemModel $model,
        public readonly ClienteModel $clienteModel,
        public readonly VeiculoModel $veiculoModel,
    ) {}

    public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    {
        $modelResult = $this->model->query()->where($nomeIdentificador, $identificador);

        if ($modelResult->exists()) {
            $modelValue = $modelResult->first();

            return (new Mapper())->fromModelToEntity($modelValue);
        }

        return null;
    }

    public function criar(string $clienteUuid, string $veiculoUuid, array $dados): array
    {
        $cliente = $this->clienteModel->query()->where('uuid', $clienteUuid)->first();
        $veiculo = $this->veiculoModel->query()->where('uuid', $veiculoUuid)->first();

        $dados['cliente_id'] = $cliente->id;
        $dados['veiculo_id'] = $veiculo->id;

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

    public function obterOrdensDoClienteComStatus(string $clienteUuid, string $status): array
    {
        $res = $this->model->query()
            ->where('cliente_uuid', $clienteUuid)
            ->where('status', $status)
            ->get(['*'])
            ->toArray();

        return $res;
    }

    public function obterOrdensDoClienteComStatusDiferenteDe(string $clienteUuid, string $status): array
    {
        $cliente = $this->clienteModel->query()->where('uuid', $clienteUuid)->first();

        $res = $this->model->query()
            ->where('cliente_id', $cliente->id)
            ->where('status', '!=', $status)
            ->get(['*'])
            ->toArray();

        return $res;
    }
}

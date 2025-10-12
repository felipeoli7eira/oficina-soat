<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Models\ClienteModel;
use App\Models\OrdemModel;
use App\Models\VeiculoModel;
use App\Models\ServicoModel;
use App\Models\MaterialModel;

use Illuminate\Support\Str;
use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Ordem\RepositorioInterface;
use App\Domain\Entity\Ordem\Mapper;
use App\Exception\DomainHttpException;
use Illuminate\Support\Facades\DB;

class OrdemEloquentRepository implements RepositorioInterface
{
    public function __construct(
        public readonly OrdemModel $model,
        public readonly ClienteModel $clienteModel,
        public readonly VeiculoModel $veiculoModel,
        public readonly ServicoModel $servicoModel,
        public readonly MaterialModel $materialModel,
    ) {}

    public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    {
        $model = $this->model->query()->where($nomeIdentificador, $identificador);

        if (! $model->exists()) {
            return null;
        }

        return (new Mapper())->fromModelToEntity($model->with(['cliente', 'veiculo'])->first());
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
            ->with(['cliente', 'veiculo'])
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

        $updated = $model->refresh()->with(['cliente', 'veiculo'])->get();

        return $updated->first()->toArray();
    }

    public function atualizarStatus(string $uuid, string $novoStatus): array
    {
        $model = $this->model->query()->where('uuid', $uuid)->first();

        $model->update([
            'status' => $novoStatus
        ]);

        $updated = $model->refresh()->with(['cliente', 'veiculo'])->get();

        return $updated->first()->toArray();
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

    public function adicionarServico(string $ordemUuid, string $servicoUuid): string
    {
        $ordem = $this->model->query()->where('uuid', $ordemUuid)->first();
        $servico = $this->servicoModel->query()->where('uuid', $servicoUuid)->first();

        $id = DB::table('os_servico')->insertGetId([
            'uuid'       => Str::uuid()->toString(),
            'os_id'      => $ordem->id,
            'servico_id' => $servico->id,
        ]);

        if (! $id) {
            throw new DomainHttpException('Erro ao adicionar serviço', 500);
        }

        return DB::table('os_servico')->where('id', $id)->first()->uuid;
    }

    public function removerServico(string $ordemUuid, string $servicoUuid): int
    {
        $ordemId = $this->model->query()->where('uuid', $ordemUuid)->first()->id;
        $servicoId = $this->servicoModel->query()->where('uuid', $servicoUuid)->first()->id;

        $rowCount = DB::table('os_servico')
            ->where('os_id', $ordemId)
            ->where('servico_id', $servicoId)
            ->delete();

        if (! $rowCount) {
            throw new DomainHttpException('Erro ao remover serviço', 500);
        }

        return $rowCount;
    }

    public function adicionarMaterial(string $ordemUuid, string $materialUuid): string
    {
        $ordem = $this->model->query()->where('uuid', $ordemUuid)->first();
        $material = $this->materialModel->query()->where('uuid', $materialUuid)->first();

        $id = DB::table('os_material')->insertGetId([
            'uuid'        => Str::uuid()->toString(),
            'os_id'       => $ordem->id,
            'material_id' => $material->id,
        ]);

        if (! $id) {
            throw new DomainHttpException('Erro ao adicionar o material na ordem de serviço', 500);
        }

        return DB::table('os_material')->where('id', $id)->first()->uuid;
    }

    public function removerMaterial(string $ordemUuid, string $materialUuid): int
    {
        $ordemId = $this->model->query()->where('uuid', $ordemUuid)->first()->id;
        $materialId = $this->materialModel->query()->where('uuid', $materialUuid)->first()->id;

        $rowCount = DB::table('os_material')
            ->where('os_id', $ordemId)
            ->where('material_id', $materialId)
            ->delete();

        if (! $rowCount) {
            throw new DomainHttpException('Erro ao remover material', 500);
        }

        return $rowCount;
    }
}

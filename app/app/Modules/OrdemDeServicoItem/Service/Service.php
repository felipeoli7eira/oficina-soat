<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoItem\Service;

use App\Modules\PecaInsumo\Repository\PecaInsumoRepository;
use App\Modules\OrdemDeServico\Repository\Repository as OrdemDeServicoRepository;
use App\Modules\OrdemDeServicoItem\Repository\Repository as OSItemRepository;

use App\Modules\OrdemDeServicoItem\Dto\AtualizacaoDto;
use App\Modules\OrdemDeServicoItem\Dto\CadastroDto;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use DomainException;

class Service
{
    public function __construct(
        private readonly OSItemRepository $repo,
        private readonly PecaInsumoRepository $pecaInsumoRepo,
        private readonly OrdemDeServicoRepository $ordemDeServicoRepo,
    ) {}

    public function listagem(): ResourceCollection|LengthAwarePaginator
    {
        return $this->repo->read();
    }

    public function pecaInsumo(): Model
    {
        return $this->pecaInsumoRepo->model();
    }

    public function ordemDeServico(): Model
    {
        return $this->ordemDeServicoRepo->model();
    }

    public function cadastro(CadastroDto $dto)
    {
        $data = $dto->asArray();

        $pecaInsumo = $this->pecaInsumo()->where('uuid', $data['peca_insumo_uuid'])->firstOrFail();

        $ordemDeServico = $this->ordemDeServico()->where('uuid', $data['os_uuid'])->firstOrFail();
        $data['peca_insumo_id'] = $pecaInsumo->id;
        $data['os_id'] = $ordemDeServico->id;

        return $this->repo->createOrFirst($data)->fresh(['pecaInsumo', 'ordemDeServico']);
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->with(['pecaInsumo', 'ordemDeServico'])->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $item = $this->obterUmPorUuid($uuid);
        $payload = $dto->asArray();

        unset($payload['uuid']);
        unset($payload['peca_insumo_id']);
        unset($payload['os_id']);

        $item->update($payload);

        return $item->refresh(['pecaInsumo', 'ordemDeServico']);
    }
}

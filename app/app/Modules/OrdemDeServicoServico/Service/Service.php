<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoServico\Service;

use App\Modules\Servico\Repository\ServicoRepository;
use App\Modules\OrdemDeServico\Repository\Repository as OrdemDeServicoRepository;
use App\Modules\OrdemDeServicoServico\Repository\Repository as OSItemRepository;

use App\Modules\OrdemDeServicoServico\Dto\AtualizacaoDto;
use App\Modules\OrdemDeServicoServico\Dto\CadastroDto;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use DomainException;

class Service
{
    public function __construct(
        private readonly OSItemRepository $repo,
        private readonly ServicoRepository $servicoRepo,
        private readonly OrdemDeServicoRepository $ordemDeServicoRepo,
    ) {}

    public function listagem(): ResourceCollection|LengthAwarePaginator
    {
        return $this->repo->read();
    }

    public function servico(): Model
    {
        return $this->servicoRepo->model();
    }

    public function ordemDeServico(): Model
    {
        return $this->ordemDeServicoRepo->model();
    }

    public function cadastro(CadastroDto $dto)
    {
        $data = $dto->asArray();

        $servico = $this->servico()->where('uuid', $data['servico_uuid'])->firstOrFail();

        $ordemDeServico = $this->ordemDeServico()->where('uuid', $data['os_uuid'])->firstOrFail();
        $data['servico_id'] = $servico->id;
        $data['os_id'] = $ordemDeServico->id;

        return $this->repo->createOrFirst($data)->fresh(['servico', 'ordemDeServico']);
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->with(['servico', 'ordemDeServico'])->firstOrFail();
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
        unset($payload['servico_id']);
        unset($payload['os_id']);

        $item->update($payload);

        return $item->refresh(['servico', 'ordemDeServico']);
    }
}

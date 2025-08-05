<?php

declare(strict_types=1);

namespace App\Modules\OS\Service;

use App\Enums\Papel;
use App\Modules\Cliente\Repository\ClienteRepository;
use App\Modules\OS\Dto\AtualizacaoDto;
use App\Modules\OS\Dto\CadastroDto;

use App\Modules\OS\Repository\Repository as OSRepository;
use App\Modules\Usuario\Repository\UsuarioRepository;
use App\Modules\Veiculo\Repository\VeiculoRepository;
use DomainException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class Service
{
    public function __construct(
        private readonly OSRepository $repo,
        private readonly UsuarioRepository $usuarioRepo,
        private readonly ClienteRepository $clienteRepo,
        private readonly VeiculoRepository $veiculoRepo,
    ) {}

    public function listagem(): ResourceCollection|LengthAwarePaginator
    {
        return $this->repo->read();
    }

    public function usuario(): Model
    {
        return $this->usuarioRepo->model();
    }

    public function cliente(): Model
    {
        return $this->clienteRepo->model();
    }

    public function veiculo(): Model
    {
        return $this->veiculoRepo->model();
    }

    public function cadastro(CadastroDto $dto)
    {
        $data = $dto->asArray();

        $usuarioAtendente = $this->usuario()->where('uuid', $data['usuario_uuid_atendente'])->firstOrFail();

        if (! $usuarioAtendente->hasRole(Papel::ATENDENTE->value)) {
            throw new DomainException('Usuário informado como atendente, não tem esse papel', Response::HTTP_BAD_REQUEST);
        }

        $usuarioMecanico = $this->usuario()->where('uuid', $data['usuario_uuid_mecanico'])->firstOrFail();

        if (! $usuarioMecanico->hasRole(Papel::MECANICO->value)) {
            throw new DomainException('Usuário informado como mecânico, não tem esse papel', Response::HTTP_BAD_REQUEST);
        }

        $cliente = $this->cliente()->where('uuid', $data['cliente_uuid'])->firstOrFail();
        $veiculo = $this->veiculo()->where('uuid', $data['veiculo_uuid'])->firstOrFail();

        unset(
            $data['cliente_uuid'],
            $data['veiculo_uuid'],
            $data['usuario_uuid_atendente'],
            $data['usuario_uuid_mecanico'],
        );

        $data['cliente_id'] = $cliente->id;
        $data['veiculo_id'] = $veiculo->id;

        $data['usuario_id_atendente'] = $usuarioAtendente->id;
        $data['usuario_id_mecanico'] = $usuarioMecanico->id;

        // dd($data);
        return $this->repo->createOrFirst($data)->fresh();
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->with(['role'])->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $usuario = $this->obterUmPorUuid($uuid);

        $novosDados = $dto->merge($usuario->toArray());

        $usuario->update($novosDados);

        return $usuario->refresh();
    }
}

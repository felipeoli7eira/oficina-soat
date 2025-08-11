<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServico\Service;

use App\Enums\Papel;

use App\Modules\Cliente\Repository\ClienteRepository;
use App\Modules\Usuario\Repository\UsuarioRepository;
use App\Modules\Veiculo\Repository\VeiculoRepository;
use App\Modules\OrdemDeServico\Repository\Repository as OSRepository;

use App\Modules\OrdemDeServico\Dto\AtualizacaoDto;
use App\Modules\OrdemDeServico\Dto\CadastroDto;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use DomainException;

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

        return $this->repo->createOrFirst($data)->fresh(['cliente', 'veiculo']);
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()
            ->where('uuid', $uuid)
            ->with(['cliente', 'veiculo', 'atendente', 'mecanico', 'ordemDeServicoItems'])->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $os = $this->obterUmPorUuid($uuid);

        $osArray = $os->toArray();

        $payload = $dto->asArray();

        if (array_key_exists('veiculo_uuid', $payload)) {
            if ($payload['veiculo_uuid'] !== $osArray['veiculo']['uuid']) {
                $novoVeiculo = $this->veiculo()->where('uuid', $payload['veiculo_uuid'])->first();

                $payload['veiculo_id'] = $novoVeiculo->id;
            }

            unset($payload['veiculo_uuid']);
        }

        if (array_key_exists('cliente_uuid', $payload)) {
            if ($payload['cliente_uuid'] !== $osArray['cliente']['uuid']) {

                $novoCliente = $this->cliente()->where('uuid', $payload['cliente_uuid'])->first();

                $payload['cliente_id'] = $novoCliente->id;
            }

            unset($payload['cliente_uuid']);
        }

        if (array_key_exists('usuario_uuid_atendente', $payload)) {
            if ($payload['usuario_uuid_atendente'] !== $osArray['atendente']['uuid']) {

                $novoPossivelAtendente = $this->usuario()->where('uuid', $payload['usuario_uuid_atendente'])->first();

                if (! $novoPossivelAtendente->hasRole(Papel::ATENDENTE->value)) {
                    throw new DomainException('Usuário informado como novo atendente não tem esse papel', Response::HTTP_BAD_REQUEST);
                }

                $payload['usuario_id_atendente'] = $novoPossivelAtendente->id;
            }

            unset($payload['usuario_uuid_atendente']);
        }

        if (array_key_exists('usuario_uuid_mecanico', $payload)) {
            if ($payload['usuario_uuid_mecanico'] !== $osArray['mecanico']['uuid']) {

                $novoPossivelMecanico = $this->usuario()->where('uuid', $payload['usuario_uuid_mecanico'])->first();

                if (! $novoPossivelMecanico->hasRole(Papel::MECANICO->value)) {
                    throw new DomainException('Usuário informado como novo mecânico não tem esse papel', Response::HTTP_BAD_REQUEST);
                }

                $payload['usuario_id_mecanico'] = $novoPossivelMecanico->id;
            }

            unset($payload['usuario_uuid_mecanico']);
        }

        $os->update($payload);

        return $os->refresh(['cliente', 'veiculo']);
    }

    public function encerrar(string $uuid)
    {
        $os = $this->obterUmPorUuid($uuid);

        $os->update([
            'data_finalizacao' => now()->format('Y-m-d H:i:s')
        ]);

        return $os->refresh(['cliente', 'veiculo']);
    }
}

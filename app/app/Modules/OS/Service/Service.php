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

        return $this->repo->createOrFirst($data)->fresh(['cliente', 'veiculo']);
    }

    public function obterUmPorUuid(string $uuid)
    {
        return $this->repo->model()->where('uuid', $uuid)->with(['cliente', 'veiculo', 'atendente', 'mecanico'])->firstOrFail();
    }

    public function remocao(string $uuid)
    {
        return $this->obterUmPorUuid($uuid)->delete();
    }

    public function atualizacao(string $uuid, AtualizacaoDto $dto)
    {
        $os = $this->obterUmPorUuid($uuid);

        $osArray = $os->toArray();
            // dd($osArray);

        $dadosParaUpdate = [];

        $payload = $dto->asArray();

        if (array_key_exists('valor_desconto', $payload)) {
            $dadosParaUpdate['valor_desconto'] = $payload['valor_desconto'];
        }

        if (array_key_exists('prazo_validade', $payload)) {
            $dadosParaUpdate['prazo_validade'] = $payload['prazo_validade'];
        }

        if (array_key_exists('valor_total', $payload)) {
            $dadosParaUpdate['valor_total'] = $payload['valor_total'];
        }


        // if (
        //     array_key_exists('cliente_uuid', $dadosParaAtualizacao)
        //     &&
        //     $dadosParaAtualizacao['cliente_uuid'] !== $osArray['cliente']['uuid']
        // ) {
        //     $novoCliente = $this->cliente()->where('uuid', $dadosParaAtualizacao['cliente_uuid'])->first();

        //     $dadosParaUpdate['cliente_id'] = $novoCliente->id;

        //     unset($dadosParaAtualizacao['cliente_uuid']);
        // }

        // if (
        //     array_key_exists('veiculo_uuid', $dadosParaAtualizacao)
        //     &&
        //     $dadosParaAtualizacao['veiculo_uuid'] !== $osArray['veiculo']['uuid']
        // ) {
        //     $novoVeiculo = $this->veiculo()->where('uuid', $dadosParaAtualizacao['veiculo_uuid'])->first();

        //     $dadosParaUpdate['veiculo_id'] = $novoVeiculo->id;

        //     unset($dadosParaAtualizacao['veiculo_uuid']);
        // }


        // dd($dadosParaUpdate, $dadosParaAtualizacao);
        dd($dadosParaUpdate);

        if (
            array_key_exists('usuario_uuid_atendente', $dadosParaAtualizacao)
            &&
            $dadosParaAtualizacao['usuario_uuid_atendente'] !== $osArray['atendente']['uuid']
        ) {
            $novoPossivelAtendente = $this->usuario()->where('uuid', $dadosParaAtualizacao['usuario_uuid_atendente'])->first();

            if (! $novoPossivelAtendente->hasRole(Papel::ATENDENTE->value)) {
                throw new DomainException('Usuário informado como novo atendente, não tem esse papel', Response::HTTP_BAD_REQUEST);
            }

        //     dd($novoPossivelAtendente);

        //     $atendenteAtualDaOs = $this->usuario()->where('uuid', $osArray['cliente']['uuid'])->first();

            // if ($atendenteAtualDaOs->uuid === $dadosParaAtualizacao['cliente_uuid']) {
            //     unset($dadosParaAtualizacao['cliente_uuid']);
            // }
        }

        // if (array_key_exists('usuario_uuid_mecanico', $dadosParaAtualizacao)) {
        //     $veiculoAtualDaOs = $this->veiculo()->where('uuid', $osArray['veiculo']['uuid'])->first();

        //     if ($veiculoAtualDaOs->uuid === $dadosParaAtualizacao['veiculo_uuid']) {
        //         unset($dadosParaAtualizacao['veiculo_uuid']);
        //     }
        // }

        dd($dadosParaAtualizacao);

        // $osArrayDadosAntigos = [
        //     'cliente_uuid'             => $this->cliente_uuid,
        //     'veiculo_uuid'             => $this->veiculo_uuid,
        //     'descricao'                => $this->descricao,
        //     'valor_desconto'           => $this->valor_desconto,
        //     'valor_total'              => $this->valor_total,
        //     'usuario_uuid_atendente'   => $this->usuario_uuid_atendente,
        //     'usuario_uuid_mecanico'    => $this->usuario_uuid_mecanico,
        //     'prazo_validade'           => $this->prazo_validade
        // ];

        // $novosDados = $dto->merge($osArrayDadosAntigos);

        // dd($novosDados);



        // $usuario->update($novosDados);

        // return $usuario->refresh();
    }

    public function finalizar(string $uuid, AtualizacaoDto $dto)
    {
        $os = $this->obterUmPorUuid($uuid);

        $novosDados = $dto->merge($os->toArray());

        dd($novosDados);



        // $usuario->update($novosDados);

        // return $usuario->refresh();
    }
}

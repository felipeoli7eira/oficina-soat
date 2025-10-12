<?php

declare(strict_types=1);

namespace App\Domain\Entity\Ordem;

use App\Domain\Entity\Cliente\Entidade as ClienteEntidade;
use App\Domain\Entity\Veiculo\Entidade as VeiculoEntidade;
use App\Models\OrdemModel;
use DateTimeImmutable;

class Mapper
{
    public function __construct() {}

    public function fromModelToEntity(OrdemModel $m): Entidade
    {
        return new Entidade(
            $m->uuid,
            new ClienteEntidade(
                $m->cliente->uuid,
                $m->cliente->nome,
                $m->cliente->documento,
                $m->cliente->email,
                $m->cliente->fone,
                new DateTimeImmutable($m->cliente->criado_em),
                new DateTimeImmutable($m->cliente->atualizado_em),
            ),
            new VeiculoEntidade(
                $m->veiculo->uuid,
                $m->veiculo->marca,
                $m->veiculo->modelo,
                $m->veiculo->placa,
                $m->veiculo->ano,
                $m->veiculo->cliente_id,
                new DateTimeImmutable($m->veiculo->criado_em),
                new DateTimeImmutable($m->veiculo->atualizado_em),
            ),
            $m->descricao,
            $m->status,
            new DateTimeImmutable($m->dt_abertura),
            $m->dt_finalizacao ? new DateTimeImmutable($m->dt_finalizacao) : null,
            $m->dt_atualizacao ? new DateTimeImmutable($m->dt_atualizacao) : null,
        );
    }

    // public function fromEntityToModel(Entidade $e): UsuarioModel
    // {
    //     $m = new UsuarioModel();

    //     $m->uuid = $e->uuid;
    //     $m->nome = $e->nome;
    //     $m->email = $e->email;
    //     $m->senha = $e->senha;
    //     $m->ativo = $e->ativo;
    //     $m->perfil = $e->perfil;
    //     $m->criado_em = $e->criadoEm;
    //     $m->atualizado_em = $e->atualizadoEm;
    //     $m->deletado_em = $e->deletadoEm;

    //     return $m;
    // }

    // public function fromModelToArray(UsuarioModel $model): array
    // {
    //     return [
    //         'uuid'          => $model->uuid,
    //         'nome'          => $model->nome,
    //         'email'         => $model->email,
    //         'senha'         => $model->senha,
    //         'ativo'         => $model->ativo,
    //         'perfil'        => $model->perfil,
    //         'criado_em'     => $model->criado_em,
    //         'atualizado_em' => $model->atualizado_em,
    //         'deletado_em'   => $model->deletado_em,
    //     ];
    // }

    // public function fromArrayToModel(array $array): UsuarioModel
    // {
    //     $model = new UsuarioModel();

    //     $model->uuid = $array['uuid'];
    //     $model->nome = $array['nome'];
    //     $model->email = $array['email'];
    //     $model->senha = $array['senha'];
    //     $model->ativo = $array['ativo'];
    //     $model->perfil = $array['perfil'];
    //     $model->criado_em = $array['criado_em'];
    //     $model->atualizado_em = $array['atualizado_em'];
    //     $model->deletado_em = $array['deletado_em'];

    //     return $model;
    // }

    // public function fromDtoToEntity(UsuarioDto $dto): Entidade
    // {
    //     return new Entidade(
    //         $dto->uuid,
    //         $dto->nome,
    //         $dto->email,
    //         $dto->senha,
    //         $dto->ativo,
    //         $dto->perfil,
    //         $dto->criado_em,
    //         $dto->atualizado_em,
    //         $dto->deletado_em,
    //     );
    // }
}

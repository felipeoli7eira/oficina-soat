<?php

declare(strict_types=1);

namespace App\Domain\Entity\Material;

use App\Models\MaterialModel;
use DateTimeImmutable;

class Mapper
{
    public function __construct() {}

    public function fromModelToEntity(MaterialModel $m): Entidade
    {
        return new Entidade(
            uuid: $m->uuid,
            nome: $m->nome,
            gtin: $m->gtin,
            estoque: $m->estoque,
            sku: $m->sku,
            descricao: $m->descricao,
            preco_custo: $m->preco_custo,
            preco_venda: $m->preco_venda,
            preco_uso_interno: $m->preco_uso_interno,
            criadoEm: new DateTimeImmutable($m->criado_em),
            atualizadoEm: new DateTimeImmutable($m->atualizado_em),
            deletadoEm: $m->deletado_em ? new DateTimeImmutable($m->deletado_em) : null,
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

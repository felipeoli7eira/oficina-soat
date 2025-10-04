<?php

declare(strict_types=1);

namespace App\Domain\Entity\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Models\UsuarioModel;
use DateTimeImmutable;

class Mapper
{
    public function __construct() {}

    public function fromModelToEntity(UsuarioModel $m): Entidade
    {
        return new Entidade(
            $m->uuid,
            $m->nome,
            $m->email,
            $m->senha,
            $m->ativo,
            new DateTimeImmutable($m->criado_em),
            new DateTimeImmutable($m->atualizado_em),
            $m->deletado_em ? new DateTimeImmutable($m->deletado_em) : null,
        );
    }

    public function fromEntityToModel(Entidade $e): UsuarioModel
    {
        $m = new UsuarioModel();

        $m->uuid = $e->identificadorUnicoUniversal;
        $m->nome = $e->nome;
        $m->email = $e->email;
        $m->senha = $e->senha;
        $m->ativo = $e->ativo;
        $m->criado_em = $e->criadoEm;
        $m->atualizado_em = $e->atualizadoEm;
        $m->deletado_em = $e->deletadoEm;

        return $m;
    }

    public function fromModelToArray(UsuarioModel $model): array
    {
        return [
            'uuid' => $model->uuid,
            'nome' => $model->nome,
            'email' => $model->email,
            'senha' => $model->senha,
            'ativo' => $model->ativo,
            'criado_em' => $model->criado_em,
            'atualizado_em' => $model->atualizado_em,
            'deletado_em' => $model->deletado_em,
        ];
    }

    public function fromArrayToModel(array $array): UsuarioModel
    {
        $model = new UsuarioModel();

        $model->uuid = $array['uuid'];
        $model->nome = $array['nome'];
        $model->email = $array['email'];
        $model->senha = $array['senha'];
        $model->ativo = $array['ativo'];
        $model->criado_em = $array['criado_em'];
        $model->atualizado_em = $array['atualizado_em'];
        $model->deletado_em = $array['deletado_em'];

        return $model;
    }
}

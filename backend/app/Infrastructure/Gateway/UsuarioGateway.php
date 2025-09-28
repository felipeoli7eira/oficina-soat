<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Usuario\Entidade;
use App\Domain\Usuario\RepositorioInterface;
use App\Infrastructure\Dto\UsuarioDto;
use App\Models\UsuarioModel;

class UsuarioGateway
{
    public function __construct(public readonly RepositorioInterface $repositorio) {}

    public function encontrarPorIdentificadorUnico(
        string $identificador,
        string $nomeIdentificador
    ): ?Entidade {
        return $this->repositorio->encontrarPorIdentificadorUnico(
            $identificador,
            $nomeIdentificador
        );
    }

    // public function buscarPorEmail(string $email): ?EntidadeUsuario
    // {
    //     $usuarioModel = UsuarioModel::where('email', $email)->first();
    //     if (!$usuarioModel) {
    //         return null;
    //     }

    //     return new EntidadeUsuario(
    //         id: $usuarioModel->id,
    //         nome: $usuarioModel->nome,
    //         email: $usuarioModel->email,
    //         senha: $usuarioModel->senha
    //     );
    // }

    public function criar(UsuarioDto $dados): Entidade
    {
        return $this->repositorio->criar($dados);
    }

    public function listar(): array
    {
        return $this->repositorio->listar();
    }

    public function deletar(string $uuid): bool
    {
        return $this->repositorio->deletar($uuid);
    }

    public function atualizar(UsuarioDto $dados): Entidade
    {
        return $this->repositorio->atualizar($dados);
    }
}

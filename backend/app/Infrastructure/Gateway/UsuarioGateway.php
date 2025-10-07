<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\RepositorioInterface;
use App\Infrastructure\Dto\UsuarioDto;

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

    public function criar(array $dados): array
    {
        return $this->repositorio->criar($dados);
    }

    public function listar(): array
    {
        return $this->repositorio->listar([
            'uuid',
            'nome',
            'email',
            'ativo',
            'criado_em',
            'atualizado_em',
        ]);
    }

    public function deletar(string $uuid): bool
    {
        return $this->repositorio->deletar($uuid);
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        return $this->repositorio->atualizar($uuid, $novosDados);
    }
}

<?php

declare(strict_types=1);

namespace App\Aplicacao\Usuario;

trait Leitura
{
    public function leitura(): array
    {
        return [];
    }

    // public function executar(Input $input): Output
    // {
    //     if ($this->usuarioGateway->buscarPorEmail($input->email)) {
    //         throw new \Exception('Email já cadastrado.');
    //     }

    // Entidade de domínio
    //     $usuario = new Usuario(
    //         id: null,
    //         nome: $input->nome,
    //         email: $input->email,
    //         senha: password_hash($input->senha, PASSWORD_DEFAULT) // Regra de negócio aqui
    //     );

    //     $novoUsuario = $this->usuarioGateway->salvar($usuario);

    //     return new Output(
    //         id: $novoUsuario->id,
    //         nome: $novoUsuario->nome,
    //         email: $novoUsuario->email
    //     );
    // }
}

<?php

namespace App\InterfaceAdaptador\Gateway;

use App\Dominio\Entidade\Usuario;
use App\Dominio\Gateway\UsuarioGateway;
use App\Models\UsuarioModel;

class UsuarioGatewayImpl implements UsuarioGateway
{
    public function buscarPorEmail(string $email): ?Usuario
    {
        $usuarioModel = UsuarioModel::where('email', $email)->first();
        if (!$usuarioModel) {
            return null;
        }

        return new Usuario(
            id: $usuarioModel->id,
            nome: $usuarioModel->nome,
            email: $usuarioModel->email,
            senha: $usuarioModel->senha
        );
    }

    public function salvar(Usuario $usuario): Usuario
    {
        $usuarioModel = UsuarioModel::create([
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'senha' => $usuario->getSenha(),
        ]);

        return new Usuario(
            id: $usuarioModel->id,
            nome: $usuarioModel->nome,
            email: $usuarioModel->email,
            senha: $usuarioModel->getSenha()
        );
    }
}

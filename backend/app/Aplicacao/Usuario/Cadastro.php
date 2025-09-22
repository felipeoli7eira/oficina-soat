<?php

declare(strict_types=1);

namespace App\Aplicacao\Usuario;

use App\Dominio\Usuario\Entidade\Entidade as UsuarioEntidade;
use App\Interface\Dto\Usuario\CriacaoDto;

trait Cadastro
{
    public function criar(CriacaoDto $dados): UsuarioEntidade
    {
        if ($this->repositorioDeDados->existe('email', $dados->email)) {

        }

        // usar o repositorio para validar o cadastro, por exemplo, se ja tem um cadastro com o mesmo email

        // instancio a entidade

        // persisto a entidade usando o repositorio e retorno isso
    }
}

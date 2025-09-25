<?php

namespace App\Interface\Controlador;

use App\Aplicacao\Usuario\CasoDeUso;
use App\Dominio\Usuario\Repositorio\Contrato as Repositorio;
use App\Interface\Apresentacao\Json;
use App\Interface\Dto\Usuario\CriacaoDto as CriarUsuarioDto;

class UsuarioControlador
{
    public function __construct(
        public readonly Repositorio $repositorio,
        public readonly CasoDeUso $casoDeUso
    ) {}

    public function criar(CriarUsuarioDto $dados): Json
    {
        $dados = $this->casoDeUso->criar(
            new CriarUsuarioDto(
                nome: $dados->nome,
                email: $dados->email,
                senha: $dados->senha,
                documento: $dados->documento
            )
        );

        return new Json((array) $dados);
    }

    public function listar(int $porPagina, int $pagina): Json
    {
        $dados = $this->casoDeUso->listar($porPagina, $pagina);

        return new Json((array) $dados);
    }
}

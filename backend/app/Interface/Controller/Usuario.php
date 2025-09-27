<?php

namespace App\Interface\Controller;

use App\Application\UseCase\Usuario\CriarUsuarioUseCase;
use App\Domain\Usuario\Entidade;
use App\Interface\Apresentacao\Json;
use App\Interface\Dto\UsuarioDto;
use App\Domain\Usuario\RepositorioInterface;
use App\Interface\Gateway\UsuarioGateway;

class Usuario
{
    public function __construct(public readonly RepositorioInterface $repositorio) {}

    public function criar(UsuarioDto $dados, CriarUsuarioUseCase $useCase): Entidade
    {
        $gateway = app(UsuarioGateway::class);
        $useCase->criar($dados, $gateway);

        $cadastro = $gateway->criar($dados);

        return $cadastro;
    }

    // public function listar(int $porPagina, int $pagina): Json
    // {
    //     $dados = $this->casoDeUso->listar($porPagina, $pagina);

    //     return new Json((array) $dados);
    // }
}

<?php

declare(strict_types=1);

namespace App\Aplicacao\Usuario;

use App\Dominio\Usuario\Repositorio\Contrato as Repositorio;
use App\Interface\Dto\Usuario\CriacaoDto;
use App\Dominio\Usuario\Entidade\Entidade as EntidadeUsuario;
use App\Exception\DomainHttpException;
use App\Interface\Dto\JsonPaginado;
use Symfony\Component\HttpFoundation\Response;

class CasoDeUso
{
    public function __construct(public readonly Repositorio $repositorioDeDados) {}

    public function criar(CriacaoDto $dados): EntidadeUsuario
    {
        if ($this->repositorioDeDados->encontrarPorIdentificadorUnico('email', $dados->email)) {
            throw new DomainHttpException('Ja existe um cadastro com esse email', Response::HTTP_BAD_REQUEST);
        }

        if ($this->repositorioDeDados->encontrarPorIdentificadorUnico('documento', $dados->documento)) {
            throw new DomainHttpException('Ja existe um cadastro com esse documento', Response::HTTP_BAD_REQUEST);
        }

        return $this->repositorioDeDados->criar(
            new EntidadeUsuario(
                '',
                $dados->nome,
                $dados->email,
                $dados->senha,
                $dados->documento,
                true,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
                null
            )
        );
    }

    public function listar(int $porPagina, int $pagina): JsonPaginado
    {
        return $this->repositorioDeDados->obterTodos($porPagina, $pagina);
    }
}

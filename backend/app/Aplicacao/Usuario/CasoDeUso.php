<?php

declare(strict_types=1);

namespace App\Aplicacao\Usuario;

use App\Dominio\Usuario\Repositorio\Contrato as Repositorio;
use App\Interface\Dto\Usuario\CriacaoDto;
use App\Dominio\Usuario\Entidade\Entidade as EntidadeUsuario;
use DomainException;

class CasoDeUso
{
    // use Cadastro;
    use Leitura;

    public function __construct(public readonly Repositorio $repositorioDeDados) {}

    public function criar(CriacaoDto $dados): EntidadeUsuario
    {
        if ($this->repositorioDeDados->encontrarPorIdentificadorUnico('email', $dados->email)) {
            throw new DomainException('Ja existe um cadastro com esse email');
        }

        if ($this->repositorioDeDados->encontrarPorIdentificadorUnico('documento', $dados->documento)) {
            throw new DomainException('Ja existe um cadastro com esse documento');
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
}

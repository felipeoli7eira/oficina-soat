<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Usuario\CriarUseCase as CriarUsuarioUseCase;
use App\Application\UseCase\Usuario\DeletarUseCase;
use App\Domain\Usuario\Entidade;
use App\Infrastructure\Dto\UsuarioDto;
use App\Domain\Usuario\RepositorioInterface;
use App\Infrastructure\Gateway\UsuarioGateway;

class Usuario
{
    public function __construct(public readonly RepositorioInterface $repositorio) {}

    public function criar(UsuarioDto $dados, CriarUsuarioUseCase $useCase): Entidade
    {
        $gateway = app(UsuarioGateway::class);

        $cadastro = $useCase->criar($dados, $gateway);

        return $cadastro;
    }

    public function listar(): array
    {
        return $this->repositorio->listar();
    }

    public function deletar(string $uuid, DeletarUseCase $useCase): bool
    {
        return $useCase->deletar($uuid);
    }
}

<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\Usuario\CreateUseCase;
use App\Application\UseCase\Usuario\ReadUseCase;
use App\Application\UseCase\Usuario\UpdateUseCase;
use App\Application\UseCase\Usuario\DeleteUseCase;

use App\Domain\Usuario\Entidade;
use App\Infrastructure\Dto\UsuarioDto;
use App\Domain\Usuario\RepositorioInterface;
use App\Infrastructure\Gateway\UsuarioGateway;

class Usuario
{
    public function __construct(public readonly RepositorioInterface $repositorio) {}

    public function criar(UsuarioDto $dados, CreateUseCase $useCase): Entidade
    {
        $gateway = app(UsuarioGateway::class);

        $res = $useCase->exec($dados, $gateway);

        return $res;
    }

    public function listar(ReadUseCase $useCase): array
    {
        $gateway = app(UsuarioGateway::class);

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function deletar(string $uuid, DeleteUseCase $useCase): bool
    {
        $res = $useCase->exec($uuid);

        return $res;
    }

    public function atualizar(UsuarioDto $dados, UpdateUseCase $useCase): Entidade
    {
        $gateway = app(UsuarioGateway::class);

        $res = $useCase->exec($dados, $gateway);

        return $res;
    }
}

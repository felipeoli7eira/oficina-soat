<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Usuario\CreateUseCase;
use App\Domain\UseCase\Usuario\ReadUseCase;
use App\Domain\UseCase\Usuario\UpdateUseCase;
use App\Domain\UseCase\Usuario\DeleteUseCase;

use App\Domain\Entity\Usuario\Entidade;
use App\Infrastructure\Dto\UsuarioDto;
use App\Domain\Entity\Usuario\RepositorioInterface;
use App\Domain\UseCase\Usuario\AuthenticateUseCase;
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

    public function authenticate(string $email, string $password, AuthenticateUseCase $useCase): array {
        $gateway = app(UsuarioGateway::class);

        $res = $useCase->exec($email, $password, $gateway);

        return $res;
    }
}

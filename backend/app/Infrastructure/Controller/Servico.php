<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Servico\CreateUseCase;
use App\Domain\UseCase\Usuario\ReadUseCase;
use App\Domain\UseCase\Usuario\UpdateUseCase;
use App\Domain\UseCase\Usuario\DeleteUseCase;

use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Domain\UseCase\Usuario\AuthenticateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Dto\AuthenticatedDto;
use App\Infrastructure\Gateway\ServicoGateway;

class Servico
{
    public readonly ServicoRepositorio $repositorio;

    public function __construct() {}

    public function useRepositorio(ServicoRepositorio $repositorio): self
    {
        $this->repositorio = $repositorio;
        return $this;
    }

    public function criar(string $nome, int $valor): array
    {
        if (! $this->repositorio instanceof ServicoRepositorio) {
            throw new DomainHttpException('Repositorio não definido', 500);
        }

        $gateway = new ServicoGateway($this->repositorio);
        $useCase = new CreateUseCase($nome, $valor);

        $res = $useCase->exec($gateway);

        return $res->toHttpResponse();
    }

    // public function listar(UsuarioRepositorio $repositorio): array
    // {
    //     $gateway = new UsuarioGateway($repositorio);
    //     $useCase = new ReadUseCase();

    //     $res = $useCase->exec($gateway);

    //     return $res;
    // }

    // public function deletar(string $uuid, UsuarioRepositorio $repositorio): bool
    // {
    //     $gateway = new UsuarioGateway($repositorio);
    //     $useCase = new DeleteUseCase($gateway);

    //     $res = $useCase->exec($uuid);

    //     return $res;
    // }

    // public function atualizar(string $uuid, array $novosDados, UsuarioRepositorio $repositorio): array
    // {
    //     $gateway = new UsuarioGateway($repositorio);
    //     $useCase = new UpdateUseCase($gateway);

    //     $res = $useCase->exec($uuid, $novosDados);

    //     return $res->toHttpResponse();
    // }

    // public function authenticate(string $email, string $password, AuthenticateUseCase $useCase): AuthenticatedDto
    // {
    //     $gateway = app(UsuarioGateway::class);

    //     $res = $useCase->exec($email, $password, $gateway);

    //     return $res;
    // }
}

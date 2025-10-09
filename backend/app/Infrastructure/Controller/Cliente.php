<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Cliente\CreateUseCase;
use App\Domain\UseCase\Cliente\ReadUseCase;
use App\Domain\UseCase\Cliente\ReadOneUseCase;
use App\Domain\UseCase\Cliente\UpdateUseCase;
use App\Domain\UseCase\Cliente\DeleteUseCase;

use App\Infrastructure\Gateway\ClienteGateway;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;

use App\Exception\DomainHttpException;

class Cliente
{
    public readonly ClienteRepositorio $repositorio;

    public function __construct() {}

    public function useRepositorio(ClienteRepositorio $repositorio): self
    {
        $this->repositorio = $repositorio;
        return $this;
    }

    public function criar(
        string $nome,
        string $documento,
        string $email,
        string $fone,
    ): array {
        if (! $this->repositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new ClienteGateway($this->repositorio);
        $useCase = new CreateUseCase(
            $nome,
            $documento,
            $email,
            $fone,
        );


        $res = $useCase->exec($gateway);

        return $res->toHttpResponse();
    }

    public function listar(): array
    {
        if (! $this->repositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new ClienteGateway($this->repositorio);
        $useCase = new ReadUseCase();

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function obterUm(string $uuid): ?array
    {
        if (! $this->repositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new ClienteGateway($this->repositorio);
        $useCase = new ReadOneUseCase($uuid);

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function deletar(string $uuid): bool
    {
        if (! $this->repositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new ClienteGateway($this->repositorio);
        $useCase = new DeleteUseCase($gateway);

        $res = $useCase->exec($uuid);

        return $res;
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        if (! $this->repositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new ClienteGateway($this->repositorio);
        $useCase = new UpdateUseCase($gateway);

        $res = $useCase->exec($uuid, $novosDados);

        return $res->toHttpResponse();
    }
}

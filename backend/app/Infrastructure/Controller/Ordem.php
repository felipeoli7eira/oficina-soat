<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Ordem\CreateUseCase;
use App\Domain\UseCase\Ordem\ReadUseCase;
use App\Domain\UseCase\Ordem\ReadOneUseCase;
use App\Domain\UseCase\Ordem\UpdateUseCase;
use App\Domain\UseCase\Ordem\DeleteUseCase;

use App\Infrastructure\Gateway\OrdemGateway;
use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;

use App\Exception\DomainHttpException;

class Ordem
{
    public readonly OrdemRepositorio $repositorio;

    public function __construct() {}

    public function useRepositorio(OrdemRepositorio $repositorio): self
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
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
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
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new ReadUseCase();

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function obterUm(string $uuid): ?array
    {
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new ReadOneUseCase($uuid);

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function deletar(string $uuid): bool
    {
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new DeleteUseCase($gateway);

        $res = $useCase->exec($uuid);

        return $res;
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new UpdateUseCase($gateway);

        $res = $useCase->exec($uuid, $novosDados);

        return $res->toHttpResponse();
    }
}

<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Ordem\CreateUseCase;
use App\Domain\UseCase\Ordem\ReadUseCase;
use App\Domain\UseCase\Ordem\ReadOneUseCase;
use App\Domain\UseCase\Ordem\UpdateUseCase;
use App\Domain\UseCase\Ordem\DeleteUseCase;

use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;

use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;

use App\Exception\DomainHttpException;

class Ordem
{
    public readonly OrdemRepositorio $repositorio;
    public readonly ClienteRepositorio $clienteRepositorio;
    public readonly VeiculoRepositorio $veiculoRepositorio;

    public function __construct() {}

    public function useRepositorio(OrdemRepositorio $repositorio): self
    {
        $this->repositorio = $repositorio;
        return $this;
    }

    public function useClienteRepositorio(ClienteRepositorio $clienteRepositorio): self
    {
        $this->clienteRepositorio = $clienteRepositorio;
        return $this;
    }

    public function useVeiculoRepositorio(VeiculoRepositorio $veiculoRepositorio): self
    {
        $this->veiculoRepositorio = $veiculoRepositorio;
        return $this;
    }

    public function criar(
        string $clienteUuid,
        string $veiculoUuid,
        ?string $descricao = null
    ): array {
        if (
            ! $this->repositorio instanceof OrdemRepositorio
            ||
            ! $this->clienteRepositorio instanceof ClienteRepositorio
            ||
            ! $this->veiculoRepositorio instanceof VeiculoRepositorio
        ) {
            throw new DomainHttpException('defina todas as fontes de dados necessárias: ordem, cliente e veiculo', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $clienteGateway = new ClienteGateway($this->clienteRepositorio);
        $veiculoGateway = new VeiculoGateway($this->veiculoRepositorio);

        $useCase = new CreateUseCase($clienteUuid, $veiculoUuid, $descricao);

        $res = $useCase->exec($gateway, $clienteGateway, $veiculoGateway);

        return $res->toExternal();
    }

    public function listar(): array
    {
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('defina todas as fontes de dados necessárias: ordem, cliente e veiculo', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new ReadUseCase();

        return $useCase->exec($gateway);
    }

    public function obterUm(string $uuid): ?array
    {
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('defina todas as fontes de dados necessárias: ordem, cliente e veiculo', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new ReadOneUseCase($uuid);

        return $useCase->exec($gateway);
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

        dd($res);
        // return $res->toHttpResponse();
    }
}

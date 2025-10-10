<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Veiculo\CreateUseCase;
use App\Domain\UseCase\Veiculo\ReadUseCase;
use App\Domain\UseCase\Veiculo\ReadOneUseCase;
use App\Domain\UseCase\Veiculo\UpdateUseCase;
use App\Domain\UseCase\Veiculo\DeleteUseCase;

use App\Infrastructure\Gateway\VeiculoGateway;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ClienteGateway;

class Veiculo
{
    public readonly VeiculoRepositorio $repositorio;
    public readonly ClienteRepositorio $clienteRepositorio;

    public function __construct() {}

    public function useRepositorio(VeiculoRepositorio $repositorio): self
    {
        $this->repositorio = $repositorio;
        return $this;
    }

    public function useClienteRepositorio(ClienteRepositorio $clienteRepositorio): self
    {
        $this->clienteRepositorio = $clienteRepositorio;
        return $this;
    }

    public function criar(
        string $marca,
        string $modelo,
        string $placa,
        int $ano,
        string $clienteUuid,
    ): array {
        if (! $this->repositorio instanceof VeiculoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        if (! $this->clienteRepositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados do cliente deve ser definida', 500);
        }

        $gateway = new VeiculoGateway($this->repositorio);
        $clienteGateway = new ClienteGateway($this->clienteRepositorio);
        $useCase = new CreateUseCase(
            $marca,
            $modelo,
            $placa,
            $ano,
            $clienteUuid,
        );

        $res = $useCase->exec($gateway, $clienteGateway);

        return $res->toHttpResponse();
    }

    public function listar(): array
    {
        if (! $this->repositorio instanceof VeiculoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        if (! $this->clienteRepositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados do cliente deve ser definida', 500);
        }

        $gateway = new VeiculoGateway($this->repositorio);
        $clienteGateway = new ClienteGateway($this->clienteRepositorio);
        $useCase = new ReadUseCase();

        $res = $useCase->exec($gateway, $clienteGateway);

        return $res;
    }

    public function obterUm(string $uuid): ?array
    {
        if (! $this->repositorio instanceof VeiculoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        if (! $this->clienteRepositorio instanceof ClienteRepositorio) {
            throw new DomainHttpException('fonte de dados do cliente deve ser definida', 500);
        }

        $gateway = new VeiculoGateway($this->repositorio);
        $clienteGateway = new ClienteGateway($this->clienteRepositorio);
        $useCase = new ReadOneUseCase($uuid);

        $res = $useCase->exec($gateway, $clienteGateway);

        return $res;
    }

    public function deletar(string $uuid): bool
    {
        if (! $this->repositorio instanceof VeiculoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new VeiculoGateway($this->repositorio);
        $useCase = new DeleteUseCase($gateway);

        $res = $useCase->exec($uuid);

        return $res;
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        if (! $this->repositorio instanceof VeiculoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new VeiculoGateway($this->repositorio);
        $useCase = new UpdateUseCase($gateway);

        $res = $useCase->exec($uuid, $novosDados);

        return $res->toHttpResponse();
    }
}

<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Ordem\CreateUseCase;
use App\Domain\UseCase\Ordem\ReadUseCase;
use App\Domain\UseCase\Ordem\ReadOneUseCase;
use App\Domain\UseCase\Ordem\UpdateUseCase;
use App\Domain\UseCase\Ordem\DeleteUseCase;

use App\Domain\UseCase\Ordem\AddServiceUseCase;
use App\Domain\UseCase\Ordem\RemoveServiceUseCase;

use App\Domain\UseCase\Ordem\AddMaterialUseCase;
use App\Domain\UseCase\Ordem\RemoveMaterialUseCase;


use App\Infrastructure\Gateway\OrdemGateway;
use App\Infrastructure\Gateway\ClienteGateway;
use App\Infrastructure\Gateway\VeiculoGateway;

use App\Domain\Entity\Ordem\RepositorioInterface as OrdemRepositorio;
use App\Domain\Entity\Cliente\RepositorioInterface as ClienteRepositorio;
use App\Domain\Entity\Veiculo\RepositorioInterface as VeiculoRepositorio;
use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;

use App\Domain\UseCase\Ordem\UpdateStatusUseCase;

use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use App\Infrastructure\Gateway\ServicoGateway;

class Ordem
{
    public readonly OrdemRepositorio $repositorio;
    public readonly ClienteRepositorio $clienteRepositorio;
    public readonly VeiculoRepositorio $veiculoRepositorio;
    public readonly ServicoRepositorio $servicoRepositorio;
    public readonly MaterialRepositorio $materialRepositorio;

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

    public function useServicoRepositorio(ServicoRepositorio $servicoRepositorio): self
    {
        $this->servicoRepositorio = $servicoRepositorio;
        return $this;
    }

    public function useMaterialRepositorio(MaterialRepositorio $materialRepositorio): self
    {
        $this->materialRepositorio = $materialRepositorio;
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

        return $useCase->exec($uuid, $novosDados)->toExternal();
    }

    public function atualizarStatus(string $uuid, string $novoStatus): array
    {
        if (! $this->repositorio instanceof OrdemRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $useCase = new UpdateStatusUseCase($gateway);

        return $useCase->exec($uuid, $novoStatus)->toExternal();
    }

    public function adicionaServico(string $ordemUuid, string $servicoUuid): string
    {
        if (! $this->repositorio instanceof OrdemRepositorio || ! $this->servicoRepositorio instanceof ServicoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $servicoGateway = new ServicoGateway($this->servicoRepositorio);
        $useCase = new AddServiceUseCase($gateway, $servicoGateway);

        return $useCase->exec($ordemUuid, $servicoUuid);
    }

    public function removeServico(string $ordemUuid, string $servicoUuid): bool
    {
        if (! $this->repositorio instanceof OrdemRepositorio || ! $this->servicoRepositorio instanceof ServicoRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $servicoGateway = new ServicoGateway($this->servicoRepositorio);
        $useCase = new RemoveServiceUseCase($gateway, $servicoGateway);

        return $useCase->exec($ordemUuid, $servicoUuid) >= 1;
    }

    public function adicionaMaterial(string $ordemUuid, string $materialUuid): string
    {
        if (! $this->repositorio instanceof OrdemRepositorio || ! $this->materialRepositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $materialGateway = new MaterialGateway($this->materialRepositorio);
        $useCase = new AddMaterialUseCase($gateway, $materialGateway);

        return $useCase->exec($ordemUuid, $materialUuid);
    }

    public function removeMaterial(string $ordemUuid, string $materialUuid): bool
    {
        if (! $this->repositorio instanceof OrdemRepositorio || ! $this->materialRepositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('fonte de dados deve ser definida', 500);
        }

        $gateway = new OrdemGateway($this->repositorio);
        $materialGateway = new MaterialGateway($this->materialRepositorio);
        $useCase = new RemoveMaterialUseCase($gateway, $materialGateway);

        return $useCase->exec($ordemUuid, $materialUuid) >= 1;
    }
}

<?php

namespace App\Infrastructure\Controller;

use App\Domain\UseCase\Material\CreateUseCase;
use App\Domain\UseCase\Material\ReadUseCase;
use App\Domain\UseCase\Material\ReadOneUseCase;
use App\Domain\UseCase\Material\UpdateUseCase;
use App\Domain\UseCase\Material\DeleteUseCase;

use App\Infrastructure\Gateway\MaterialGateway;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;

use App\Exception\DomainHttpException;

class Material
{
    public readonly MaterialRepositorio $repositorio;

    public function __construct() {}

    public function useRepositorio(MaterialRepositorio $repositorio): self
    {
        $this->repositorio = $repositorio;
        return $this;
    }

    public function criar(
        string $nome,
        string $gtin,
        int $estoque,
        int $preco_custo,
        int $preco_venda,
        int $preco_uso_interno,
        ?string $sku = null,
        ?string $descricao = null
    ): array {
        if (! $this->repositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('Repositorio não definido', 500);
        }

        $gateway = new MaterialGateway($this->repositorio);
        $useCase = new CreateUseCase(
            $nome,
            $gtin,
            $estoque,
            $preco_custo,
            $preco_venda,
            $preco_uso_interno,
            $sku,
            $descricao,
        );


        $res = $useCase->exec($gateway);

        return $res->toHttpResponse();
    }

    public function listar(): array
    {
        if (! $this->repositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('Repositorio não definido', 500);
        }

        $gateway = new MaterialGateway($this->repositorio);
        $useCase = new ReadUseCase();

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function obterUm(string $uuid): ?array
    {
        if (! $this->repositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('Repositorio não definido', 500);
        }

        $gateway = new MaterialGateway($this->repositorio);
        $useCase = new ReadOneUseCase($uuid);

        $res = $useCase->exec($gateway);

        return $res;
    }

    public function deletar(string $uuid): bool
    {
        if (! $this->repositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('Repositorio não definido', 500);
        }

        $gateway = new MaterialGateway($this->repositorio);
        $useCase = new DeleteUseCase($gateway);

        $res = $useCase->exec($uuid);

        return $res;
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        if (! $this->repositorio instanceof MaterialRepositorio) {
            throw new DomainHttpException('Repositorio não definido', 500);
        }

        $gateway = new MaterialGateway($this->repositorio);
        $useCase = new UpdateUseCase($gateway);

        $res = $useCase->exec($uuid, $novosDados);

        return $res->toHttpResponse();
    }
}

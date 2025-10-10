<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Entity\Veiculo\Entidade;
use App\Domain\Entity\Veiculo\RepositorioInterface;

class VeiculoGateway
{
    public function __construct(public readonly RepositorioInterface $repositorio) {}

    public function encontrarPorIdentificadorUnico(
        string $identificador,
        string $nomeIdentificador
    ): ?Entidade {
        return $this->repositorio->encontrarPorIdentificadorUnico(
            $identificador,
            $nomeIdentificador
        );
    }

    public function criar(array $dados): array
    {
        return $this->repositorio->criar($dados);
    }

    public function listar(): array
    {
        return $this->repositorio->listar(['*']);
    }

    public function deletar(string $uuid): bool
    {
        return $this->repositorio->deletar($uuid);
    }

    public function atualizar(string $uuid, array $novosDados): array
    {
        return $this->repositorio->atualizar($uuid, $novosDados);
    }
}

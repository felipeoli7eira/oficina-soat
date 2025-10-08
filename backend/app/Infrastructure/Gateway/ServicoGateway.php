<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;

class ServicoGateway
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
        return $this->repositorio->listar([
            'uuid',
            'nome',
            'valor',
            'criado_em',
            'atualizado_em',
        ]);
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

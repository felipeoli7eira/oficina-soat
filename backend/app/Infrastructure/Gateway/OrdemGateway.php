<?php

namespace App\Infrastructure\Gateway;

use App\Domain\Entity\Ordem\Entidade;
use App\Domain\Entity\Ordem\RepositorioInterface;

class OrdemGateway
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

    public function criar(string $clienteUuid, string $veiculoUuid, array $dados): array
    {
        return $this->repositorio->criar(
            $clienteUuid,
            $veiculoUuid,
            $dados
        );
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

    /**
     * Metodo responsavel por devolver um id numero, caso haja, a partir de um uuid
     *
     * @param string $uuid o uuid para ser resolvido em id numerico
     * @return int -1 para erro ou não encontrado, 1+ com o id encontrado
     */
    public function obterIdNumerico(string $uuid): int
    {
        return $this->repositorio->obterIdNumerico($uuid);
    }

    public function obterOrdensDoClienteComStatusDiferenteDe(string $clienteUuid, string $status): array
    {
        return $this->repositorio->obterOrdensDoClienteComStatusDiferenteDe($clienteUuid, $status);
    }

    public function adicionarServico(string $ordemUuid, string $servicoUuid): string
    {
        return $this->repositorio->adicionarServico($ordemUuid, $servicoUuid);
    }
    public function removerServico(string $ordemUuid, string $servicoUuid): int
    {
        return $this->repositorio->removerServico($ordemUuid, $servicoUuid);
    }
}

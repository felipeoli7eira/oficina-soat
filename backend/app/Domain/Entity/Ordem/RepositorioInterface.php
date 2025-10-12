<?php

declare(strict_types=1);

namespace App\Domain\Entity\Ordem;

interface RepositorioInterface
{
    public function encontrarPorIdentificadorUnico(
        string|int $identificador,
        ?string $nomeIdentificador
    ): ?Entidade;

    public function criar(string $clienteUuid, string $veiculoUuid, array $dados): array;
    public function listar(array $columns = ['*']): array;
    public function deletar(string $uuid): bool;
    public function atualizar(string $uuid, array $novosDados): array;
    public function obterIdNumerico(string $uuid): int;
    public function obterOrdensDoClienteComStatus(string $clienteUuid, string $status): array;
    public function obterOrdensDoClienteComStatusDiferenteDe(string $clienteUuid, string $status): array;
    public function adicionarServico(string $ordemUuid, string $servicoUuid): string;
    public function removerServico(string $ordemUuid, string $servicoUuid): int;
    public function atualizarStatus(string $uuid, string $novoStatus): array;
    public function adicionarMaterial(string $ordemUuid, string $materialUuid): string;
    public function removerMaterial(string $ordemUuid, string $materialUuid): int;
}

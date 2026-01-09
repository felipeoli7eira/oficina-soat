<?php

declare(strict_types=1);

namespace App\Domain\Entity\Cliente;

interface RepositorioInterface
{
    public function encontrarPorIdentificadorUnico(
        string|int $identificador,
        ?string $nomeIdentificador
    ): ?Entidade;

    public function criar(array $dados): array;
    public function listar(array $columns = ['*']): array;
    public function deletar(string $uuid): bool;
    public function atualizar(string $uuid, array $novosDados): array;
    public function obterIdNumerico(string $uuid): int;
    public function validaStatus(string $documento): bool;
}

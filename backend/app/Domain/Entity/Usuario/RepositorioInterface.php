<?php

declare(strict_types=1);

namespace App\Domain\Entity\Usuario;

use App\Domain\Entity\Usuario\Entidade;

interface RepositorioInterface
{
    public function encontrarPorIdentificadorUnico(
        string|int $identificador
        /** cnpj, cpf, uuid, email, id */
        ,
        ?string $nomeIdentificador
    ): ?Entidade;

    public function criar(array $dados): array;

    public function listar(array $columns = ['*']): array;

    public function deletar(string $uuid): bool;

    public function atualizar(string $uuid, array $novosDados): array;
}

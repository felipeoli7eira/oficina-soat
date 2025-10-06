<?php

declare(strict_types=1);

namespace App\Domain\Entity\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Infrastructure\Dto\UsuarioDto;

interface RepositorioInterface
{
    public function encontrarPorIdentificadorUnico(
        string|int $identificador
        /** cnpj, cpf, uuid, email, id */
        ,
        ?string $nomeIdentificador
    ): ?Entidade;

    public function criar(UsuarioDto $dados): Entidade;

    public function listar(array $columns = ['*']): array;

    public function deletar(string $uuid): bool;

    public function atualizar(UsuarioDto $dados): Entidade;
}

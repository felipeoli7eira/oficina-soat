<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

use App\Domain\Usuario\Entidade;
use App\Interface\Dto\UsuarioDto;

interface RepositorioInterface
{
    public function encontrarPorIdentificadorUnico(
        string|int $identificador
        /** cnpj, cpf, uuid, email, id */
        ,
        ?string $nomeIdentificador
    ): ?Entidade;

    public function criar(UsuarioDto $dados): Entidade;
}

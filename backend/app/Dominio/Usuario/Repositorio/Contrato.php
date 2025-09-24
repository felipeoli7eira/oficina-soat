<?php

declare(strict_types=1);

namespace App\Dominio\Usuario\Repositorio;

use App\Dominio\Usuario\Entidade\Entidade as EntidadeUsuario;
use App\Interface\Dto\JsonPaginado;

interface Contrato
{
    public function obterTodos(int $porPagina, int $pagina): JsonPaginado;
    public function encontrarPorIdentificadorUnico(string $identificador /** cnpj, cpf, uuid, email */, string $nomeIdentificador): ?EntidadeUsuario;

    public function criar(EntidadeUsuario $entidade): EntidadeUsuario;

    public function modificar(int|string $identificador, array $dados, string $nomeIdentificador): EntidadeUsuario;

    public function remover(int|string $identificador, string $nomeIdentificador): bool;
}

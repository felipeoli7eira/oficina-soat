<?php

declare(strict_types=1);

namespace App\Dominio\Usuario\Repositorio;

use stdClass;
use App\Dominio\Usuario\Entidade\Entidade as EntidadeUsuario;

interface Contrato
{
    public function obterTodos(): array;
    public function encontrarPorIdentificadorUnico(string $identificador /** cnpj, cpf, uuid, email */, string $nomeIdentificador): ?EntidadeUsuario;

    public function criar(EntidadeUsuario $entidade): EntidadeUsuario;

    public function modificar(int|string $identificador, array $dados, string $nomeIdentificador): EntidadeUsuario;

    public function remover(int|string $identificador, string $nomeIdentificador): bool;
}

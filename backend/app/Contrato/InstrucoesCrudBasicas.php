<?php

declare(strict_types=1);

namespace App\Contrato;

use stdClass;

interface InstrucoesCrudBasicas
{
    public function obterTodos(): array;
    public function encontrarPorIdentificadorNumerico(int $identificador, string $nomeIdentificador): ?array;
    public function encontrarPorIdentificadorUnicoUniversal(string $identificador, string $nomeIdentificador): ?array;
    public function criar(array $data): stdClass;
    public function modificar(int|string $identificador, array $dados, string $nomeIdentificador): stdClass;
    public function remover(int|string $identificador, string $nomeIdentificador): bool;
}

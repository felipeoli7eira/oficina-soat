<?php

declare(strict_types=1);

namespace App\Interface\Dto;

class JsonPaginado
{
    public function __construct(
        public readonly array $itens,
        public readonly int   $total,
        public readonly int   $porPagina,
        public readonly int   $paginaAtual,
        public readonly int   $ultimaPagina,
    ) {}
}

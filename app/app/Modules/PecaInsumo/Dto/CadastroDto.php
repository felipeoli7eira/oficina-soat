<?php

declare(strict_types=1);

namespace App\Modules\PecaInsumo\Dto;

class CadastroDto
{
    public function __construct(
        public string $gtin,
        public string $descricao,
        public float $valor_custo,
        public float $valor_venda,
        public int $qtd_atual,
        public int $qtd_segregada,
        public string $status
    ) {}

    public function asArray(): array
    {
        return [
            'gtin' => $this->gtin,
            'descricao' => $this->descricao,
            'valor_custo' => $this->valor_custo,
            'valor_venda' => $this->valor_venda,
            'qtd_atual' => $this->qtd_atual,
            'qtd_segregada' => $this->qtd_segregada,
            'status' => $this->status
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

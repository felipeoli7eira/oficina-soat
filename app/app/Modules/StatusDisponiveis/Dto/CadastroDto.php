<?php

declare(strict_types=1);

namespace App\Modules\StatusDisponiveis\Dto;

class CadastroDto
{
    public function __construct(
        public string $descricao,
        public int $ordem
    ) {}

    public function asArray(): array
    {
        return [
            'descricao' => $this->descricao,
            'ordem'     => $this->ordem
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

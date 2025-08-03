<?php

declare(strict_types=1);

namespace App\Modules\Servico\Dto;

class CadastroDto
{
    public function __construct(
        public string $descricao,
        public ?string $valor,
        public ?string $status
    ) {}

    public function asArray(): array
    {
        return [
            'descricao' => $this->descricao,
            'valor'     => $this->valor,
            'status'    => $this->status
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

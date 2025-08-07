<?php

declare(strict_types=1);

namespace App\Modules\ClienteVeiculo\Dto;

class CadastroDto
{
    public function __construct(
        public int $veiculoId,
        public ?int $clienteId
    ) {}

    public function asArray(): array
    {
        return [
            'cliente_id' => $this->clienteId,
            'veiculo_id' => $this->veiculoId,
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

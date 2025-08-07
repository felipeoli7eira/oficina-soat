<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Dto;

class ListagemDto
{
    public function __construct(
        public ?string $clienteUuid = null,
        public ?int $page = null,
        public ?int $perPage = null
    ) {}

    public function asArray(): array
    {
        return [
            'cliente_uuid' => $this->clienteUuid,
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn($value) => !is_null($value));
    }
}

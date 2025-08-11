<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Dto;

use Spatie\Permission\Models\Role;

class CadastroDto
{
    public function __construct(
        public string $nome,
        public string $email,
        public string $senha,
        public string $papel,
        public string $status
    ) {}

    public function asArray(): array
    {
        return [
            'nome'    => trim($this->nome),
            'email'   => trim($this->email),
            'senha'   => trim($this->senha),
            'role'    => Role::findByName(trim(strtolower($this->papel))),
            'status'  => trim($this->status),
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

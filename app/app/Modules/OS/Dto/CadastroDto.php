<?php

declare(strict_types=1);

namespace App\Modules\OS\Dto;

use Spatie\Permission\Models\Role;

class CadastroDto
{
    public function __construct(public string $nome, public string $papel, public string $status) {}

    public function asArray(): array
    {
        return [
            'nome'    => trim($this->nome),
            'role_id' => Role::findByName(trim(strtolower($this->papel)))->id,
            'status'  => trim($this->status),
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Cliente\Dto;

class ListagemDto
{
    public function __construct() {}

    public function asArray(): array
    {
        return [
        ];
    }

    public function filled(): array
    {
        return array_map(fn($value) => !is_null($value), $this->asArray());
    }
}

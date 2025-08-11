<?php

namespace App\Modules\Usuario\Enums;

enum StatusUsuario: string
{
    case ATIVO   = 'ATIVO';
    case INATIVO = 'INATIVO';

    public static function getValues(): array
    {
        return self::cases();
    }
}

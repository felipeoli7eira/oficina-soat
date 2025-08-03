<?php

namespace App\Modules\Usuario\Enums;

enum StatusUsuario: string
{
    case ATIVO   = 'ativo';
    case INATIVO = 'inativo';

    public static function getValues(): array
    {
        return self::cases();
    }
}

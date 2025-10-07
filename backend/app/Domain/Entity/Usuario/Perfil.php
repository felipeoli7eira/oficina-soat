<?php

namespace App\Domain\Entity\Usuario;

enum Perfil: string
{
    case ATENDENTE = 'atendente';
    case COMERCIAL = 'comercial';
    case MECANICO = 'mecanico';
    case GESTOR_ESTOQUE = 'gestor_estoque';

    public static function casesAsArray(): array
    {
        return array_map(fn($case) => $case->value, Perfil::cases());
    }
}

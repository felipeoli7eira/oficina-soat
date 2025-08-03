<?php

namespace App\Enums;

enum Papel: string
{
    case ATENDENTE = 'atendente';
    case COMERCIAL = 'comercial';
    case MECANICO = 'mecanico';
    case GESTOR_ESTOQUE = 'gestor_estoque';
}

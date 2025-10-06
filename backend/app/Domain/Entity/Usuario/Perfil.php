<?php

namespace App\Domain\Entity\Usuario;

enum Perfil: string
{
    case ATENDENTE = 'atendente';
    case COMERCIAL = 'comercial';
    case MECANICO = 'mecanico';
    case GESTOR_ESTOQUE = 'gestor_estoque';
}

<?php

namespace App\Modules\OrdemDeServico\Enums;

enum StatusOrdemDeServico: string
{
    case RECEBIDA                    = 'RECEBIDA';
    case EM_DIAGNOSTICO              = 'EM_DIAGNOSTICO';
    case RECUSADO_PELO_CLIENTE       = 'RECUSADO_PELO_CLIENTE';
    case DESISTENCIA_DO_CLIENTE      = 'DESISTENCIA_DO_CLIENTE';
    case EXPIRADO                    = 'EXPIRADO';
    case AGUARDANDO_APROVACAO        = 'AGUARDANDO_APROVACAO';
    case APROVADO                    = 'APROVADO';
    case AGUARDANDO_PECAS_INSUMOS    = 'AGUARDANDO_PECAS_INSUMOS';
    case PECAS_INSUMOS_DISPONIVEIS   = 'PECAS_INSUMOS_DISPONIVEIS';
    case PECAS_INSUMOS_INDISPONIVEIS = 'PECAS_INSUMOS_INDISPONIVEIS';
    case EM_EXECUSAO                 = 'EM_EXECUSAO';
    case FINALIZADA                  = 'FINALIZADA';
    case ENTREGUE                    = 'ENTREGUE';

    public static function getValues(): array
    {
        return self::cases();
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

/**
 * Role-Based Access Control (RBAC)
 * @see https://levelup.gitconnected.com/authentication-explained-when-to-use-basic-bearer-oauth2-jwt-sso-c3fb0aa083ef
*/

enum ProfileEnum: string
{
    /**
     * Realiza todo o contato necessário com o cliente:
     * - cadastra o cliente no sistema
     * - cadastra os veículos do cliente no sistema
     *
     * Gerencia ordens de serviço:
     *
     * - cria e gerencia uma ordem de serviço
     * - cadastra os materiais que serão utilizados na ordem de serviço
     * - cadastra os serviços que serão utilizados na ordem de serviço
     * - envia o orçamento de uma ordem de serviço para que o cliente aprove ou desaprove
     *
    */
    case ATENDENTE = 'atendente';

    /**
     * Realiza diagnósticos, manutenções e instalações em sistemas eletrônicos de veículos.
    */
    case TECNICO_AUTOMOTIVO = 'tecnico_automotivo';

    /**
     * Tem acesso total ao sistema e todos os módulos existentes.
     * Geralmente os devs, gerentes e outros profissionais de cargo mais estratégico terão esse perfil.
    */
    case ADMIN = 'admin';
}

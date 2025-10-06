<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Exception\DomainHttpException;
use App\Infrastructure\Dto\UsuarioDto;
use App\Infrastructure\Gateway\UsuarioGateway;

class UpdateUseCase
{
    public function __construct(public readonly UsuarioGateway $gateway) {}

    public function exec(UsuarioDto $dados, UsuarioGateway $gateway): Entidade
    {
        if (! isset($dados->uuid)) {
            throw new DomainHttpException('identificador único não informado', 400);
        }

        if (is_null($gateway->encontrarPorIdentificadorUnico($dados->uuid, 'uuid'))) {
            throw new DomainHttpException('Usuário não encontrado', 400);
        }

        $update = $gateway->atualizar($dados);

        return $update;
    }
}

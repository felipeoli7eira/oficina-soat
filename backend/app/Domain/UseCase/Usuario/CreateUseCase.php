<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

use App\Infrastructure\Gateway\UsuarioGateway;
use App\Infrastructure\Dto\UsuarioDto;
use App\Domain\Entity\Usuario\Entidade;
use App\Exception\DomainHttpException;

class CreateUseCase
{
    public function __construct() {}

    public function exec(UsuarioDto $dados, UsuarioGateway $gateway): Entidade
    {
        // regras de negocio, validacoes...

        if ($gateway->encontrarPorIdentificadorUnico($dados->email, 'email') instanceof Entidade) {
            throw new DomainHttpException('E-mail já cadastrado', 400);
        }

        $cadastro = $gateway->criar($dados);

        return $cadastro;
    }
}

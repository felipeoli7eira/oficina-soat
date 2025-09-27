<?php

declare(strict_types=1);

namespace App\Application\UseCase\Usuario;

use App\Interface\Gateway\UsuarioGateway;
use App\Interface\Dto\UsuarioDto;
use App\Domain\Usuario\Entidade;
use App\Exception\DomainHttpException;

class CriarUsuarioUseCase
{
    public function __construct() {}

    public function criar(UsuarioDto $dados, UsuarioGateway $gateway): Entidade
    {
        // regras de negocio, validacoes...

        if ($gateway->encontrarPorIdentificadorUnico($dados->email, 'email') instanceof Entidade) {
            throw new DomainHttpException('E-mail já cadastrado', 400);
        }

        return new Entidade(
            '',
            $dados->nome,
            $dados->email,
            $dados->senha,
            Entidade::STATUS_ATIVO,
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            null
        );
    }
}

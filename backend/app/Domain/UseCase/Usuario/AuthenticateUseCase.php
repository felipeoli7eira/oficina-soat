<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade as UsuarioEntity;
use App\Exception\DomainHttpException;
use App\Infrastructure\Dto\AuthenticatedDto;
use App\Infrastructure\Gateway\UsuarioGateway;
use App\Signature\AuthServiceInterface;
use App\Signature\TokenServiceInterface;
use DomainException;

class AuthenticateUseCase
{
    public function __construct(
        public readonly AuthServiceInterface $authService,
        public readonly TokenServiceInterface $tokenService,
    ) {}

    public function exec(string $email, string $plainTextPassword, UsuarioGateway $gateway): AuthenticatedDto
    {
        $usuario = $this->authService->attempt($email, $plainTextPassword);

        if (! $usuario instanceof UsuarioEntity) {
            throw new DomainHttpException('Credenciais inválidas', 401);
        }

        $token = $this->tokenService->generate($usuario->toTokenPayload());

        return new AuthenticatedDto($usuario, $token, 'Bearer');
    }
}

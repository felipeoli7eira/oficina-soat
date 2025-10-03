<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Signature\AuthServiceInterface;
use App\Domain\Entity\Usuario\Entidade;
use App\Domain\Entity\Usuario\RepositorioInterface as UsuarioRepositorio;
use Illuminate\Support\Facades\Hash;

class LaravelAuthService implements AuthServiceInterface
{
    public function __construct(private UsuarioRepositorio $usuarioRepositorio) {}

    public function attempt(string $email, string $password): ?Entidade
    {
        $user = $this->usuarioRepositorio->encontrarPorIdentificadorUnico($email, 'email');

        if (! $user instanceof Entidade) {
            return null;
        }

        if (! $user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }

    public function check(): bool
    {
        return request()->attributes->has('user');
    }

    public function user(): ?Entidade
    {
        return request()->attributes->get('user');
    }
}

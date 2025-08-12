<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use DomainException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthUsuarioService
{
    public function __construct() {}

    public function autenticarComEmailESenha(string $email, string $senha): array
    {
        $credenciais = ['email' => $email, 'password' => $senha];
        if (! $token = JWTAuth::attempt($credenciais)) {
            throw new DomainException('Credenciais inválidas', Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   =>  ((int) JWTAuth::factory()->getTTL()) * 60,
        ];
    }
}

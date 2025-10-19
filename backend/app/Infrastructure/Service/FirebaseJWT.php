<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Contract\TokenHandlerContract;
use App\Domain\Usuario\Entity;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Infrastructure\Dto\JsonWebTokenFragment;
use RuntimeException;

class FirebaseJWT implements TokenHandlerContract
{
    private string $secret;
    private string $algo = 'HS256';

    public function __construct()
    {
        if (env('JWT_SECRET') === null) {
            throw new RuntimeException('JWT_SECRET env não configurada', 500);
        }

        $this->secret = env('JWT_SECRET');
    }

    public function generate(Entity $usuario): string
    {
        $now = time();

        $payload = [
            'iss' => config('app.url'),
            'aud' => config('app.url'),
            'iat' => $now,
            'exp' => strtotime('+1 hours'), // expira em 1 hora
            'sub' => $usuario->uuid,
            'nbf' => $now, // "Not Before" - o token não é válido antes desse timestamp.

            'jti' => \Illuminate\Support\Str::uuid()->toString(), // "JWT ID" - um identificador único pra esse token específico.
            // se precisar fazer algum esquema de invalidação de token, basta guardar o JTI dele em algum lugar (Redis, DB, etc) numa lista de tokens válidos.
            // invalide ele removendo dessa lista de tokens validos
        ];

        $token = JWT::encode($payload, $this->secret, $this->algo);

        return $token;
    }

    // public function validate(string $token): ?JsonWebTokenFragment
    // {
    //     try {
    //         $decoded = JWT::decode($token, new Key($this->secret, $this->algo));
    //     } catch (Exception $_) {
    //         return null;
    //     }

    //     return new JsonWebTokenFragment(
    //         sub: $decoded->sub,
    //         iss: $decoded->iss,
    //         aud: $decoded->aud,
    //         iat: $decoded->iat,
    //         exp: $decoded->exp,
    //         nbf: $decoded->nbf,
    //     );
    // }

    // public function refresh(string $token): string
    // {
    //     $claims = $this->validate($token);

    //     if ($claims === null) {
    //         throw new Exception('Token inválido');
    //     }

    //     $c = $claims->toAssociativeArray();

    //     // Remove claims de controle
    //     unset($c['iat'], $c['exp'], $c['iss']);

    //     return $this->generate($c);
    // }

    // public function invalidate(string $token): void
    // {
    //     // Implementar blacklist (Redis, DB, etc)
    //     // Cache::put("blacklist:{$token}", true, 60 * 60 * 24);
    // }
}

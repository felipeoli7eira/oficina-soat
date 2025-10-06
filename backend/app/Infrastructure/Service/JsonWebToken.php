<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Signature\TokenServiceInterface;
use App\Infrastructure\Dto\JsonWebTokenFragment;
use RuntimeException;

class JsonWebToken implements TokenServiceInterface
{
    private string $secret;
    private string $algo = 'HS256';

    public function __construct()
    {
        if (env('JWT_SECRET') === null) {
            throw new RuntimeException('JWT_SECRET não configurado', 500);
        }

        $this->secret = env('JWT_SECRET');
    }

    public function generate(array $claims): string
    {
        $payload = [
            'iss' => config('app.url'),
            'aud' => config('app.url'),
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24h
            // 'sub' => $claims['sub'] ?? throw new InvalidArgumentException('O claim "sub" é obrigatório'),
            // 'jti' => \Illuminate\Support\Str::uuid()->toString(),
            'nbf' => time(),
            ...$claims
        ];

        return JWT::encode($payload, $this->secret, $this->algo);

        // return JWTAuth::encode(new Payload(new Collection($payload), new PayloadValidator()), $this->secret, $this->algo);
    }

    public function validate(string $token): ?JsonWebTokenFragment
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algo));
        } catch (Exception $_) {
            return null;
        }

        return new JsonWebTokenFragment(
            sub: $decoded->sub,
            iss: $decoded->iss,
            aud: $decoded->aud,
            iat: $decoded->iat,
            exp: $decoded->exp,
            nbf: $decoded->nbf,
        );
    }

    public function refresh(string $token): string
    {
        $claims = $this->validate($token);

        if ($claims === null) {
            throw new Exception('Token inválido');
        }

        $c = $claims->toAssociativeArray();

        // Remove claims de controle
        unset($c['iat'], $c['exp'], $c['iss']);

        return $this->generate($c);
    }

    public function invalidate(string $token): void
    {
        // Implementar blacklist (Redis, DB, etc)
        // Cache::put("blacklist:{$token}", true, 60 * 60 * 24);
    }
}

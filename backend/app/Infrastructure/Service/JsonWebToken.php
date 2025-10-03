<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Signature\TokenServiceInterface;
use Exception;
use InvalidArgumentException;
use Tymon\JWTAuth\Claims\Collection;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Payload;
use Tymon\JWTAuth\Validators\PayloadValidator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JsonWebToken implements TokenServiceInterface
{
    private string $secret;
    private string $algo = 'HS256';

    public function __construct()
    {
        $this->secret = config('jwt.secret');
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

    public function validate(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algo));

            return (array) $decoded;
        } catch (Exception $err) {
        }

        return null;
    }

    public function refresh(string $token): string
    {
        $claims = $this->validate($token);

        if ($claims === null) {
            throw new Exception('Token inválido');
        }

        // Remove claims de controle
        unset($claims['iat'], $claims['exp'], $claims['iss']);

        return $this->generate($claims);
    }

    public function invalidate(string $token): void
    {
        // Implementar blacklist (Redis, DB, etc)
        // Cache::put("blacklist:{$token}", true, 60 * 60 * 24);
    }
}

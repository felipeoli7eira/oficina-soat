<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected ?string $authToken = null;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Cria um usuário de teste e retorna o token JWT
     */
    protected function createAuthenticatedUser(
        string $email = 'test@example.com',
        string $senha = 'senha123',
        string $nome = 'Usuario Teste',
        string $perfil = 'atendente'
    ): string {
        // Cria o usuário diretamente no banco de dados
        \Illuminate\Support\Facades\DB::table('usuarios')->insert([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nome' => $nome,
            'email' => $email,
            'senha' => \Illuminate\Support\Facades\Hash::make($senha),
            'perfil' => $perfil,
            'ativo' => true,
        ]);

        // Faz login e obtém o token
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $email,
            'senha' => $senha,
        ]);

        if ($loginResponse->status() !== 200) {
            throw new \RuntimeException(
                'Failed to authenticate test user. Status: ' . $loginResponse->status() .
                '. Response: ' . json_encode($loginResponse->json())
            );
        }

        $this->authToken = $loginResponse->json('token');

        if (empty($this->authToken)) {
            throw new \RuntimeException(
                'Token is empty in login response. Response: ' . json_encode($loginResponse->json())
            );
        }

        return $this->authToken;
    }

    /**
     * Faz uma requisição POST autenticada
     */
    protected function authenticatedPostJson(string $uri, array $data = [], ?string $token = null)
    {
        $token = $token ?? $this->authToken ?? $this->createAuthenticatedUser();

        return $this->postJson($uri, $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    /**
     * Faz uma requisição GET autenticada
     */
    protected function authenticatedGetJson(string $uri, ?string $token = null)
    {
        $token = $token ?? $this->authToken ?? $this->createAuthenticatedUser();

        return $this->getJson($uri, [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    /**
     * Faz uma requisição PUT autenticada
     */
    protected function authenticatedPutJson(string $uri, array $data = [], ?string $token = null)
    {
        $token = $token ?? $this->authToken ?? $this->createAuthenticatedUser();

        return $this->putJson($uri, $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }

    /**
     * Faz uma requisição DELETE autenticada
     */
    protected function authenticatedDeleteJson(string $uri, ?string $token = null)
    {
        $token = $token ?? $this->authToken ?? $this->createAuthenticatedUser();

        return $this->deleteJson($uri, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
    }
}

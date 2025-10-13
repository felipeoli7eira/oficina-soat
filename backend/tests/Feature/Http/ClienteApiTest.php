<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\JsonWebTokenMiddleware;

class ClienteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(JsonWebTokenMiddleware::class);
    }

    public function testCreateComSucesso()
    {
        $response = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['uuid', 'nome', 'documento', 'email', 'fone']);
    }

    public function testCreateComNomeVazio()
    {
        $response = $this->postJson('/api/cliente', [
            'nome' => '',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComDocumentoVazio()
    {
        $response = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComEmailInvalido()
    {
        $response = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'email-invalido',
            'fone' => '11999999999',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComFoneVazio()
    {
        $response = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadRetornaListaDeClientes()
    {
        // Cria alguns clientes primeiro
        $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $this->postJson('/api/cliente', [
            'nome' => 'Maria Santos',
            'documento' => '98765432109',
            'email' => 'maria@example.com',
            'fone' => '11988888888',
        ]);

        $response = $this->getJson('/api/cliente');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['uuid', 'nome', 'documento', 'email', 'fone']
            ]);
    }

    public function testReadOneComSucesso()
    {
        $createResponse = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->getJson("/api/cliente/{$uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'uuid' => $uuid,
                'nome' => 'João Silva',
                'documento' => '12345678901',
            ]);
    }

    public function testReadOneComUuidInvalido()
    {
        $response = $this->getJson('/api/cliente/uuid-invalido');

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->getJson("/api/cliente/{$uuidNaoExistente}");

        $response->assertStatus(404);
    }

    public function testUpdateComSucesso()
    {
        $createResponse = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->putJson("/api/cliente/{$uuid}", [
            'nome' => 'João da Silva Santos',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'uuid' => $uuid,
                'nome' => 'João da Silva Santos',
            ]);
    }

    public function testUpdateComUuidInvalido()
    {
        $response = $this->putJson('/api/cliente/uuid-invalido', [
            'nome' => 'Novo Nome',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testUpdateComEmailInvalido()
    {
        $createResponse = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->putJson("/api/cliente/{$uuid}", [
            'email' => 'email-invalido',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testDeleteComSucesso()
    {
        $createResponse = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->deleteJson("/api/cliente/{$uuid}");

        $response->assertStatus(204);
    }

    public function testDeleteComUuidInvalido()
    {
        $response = $this->deleteJson('/api/cliente/uuid-invalido');

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCastsUpdateRemoveCaracteresEspeciaisDoDocumento()
    {
        $createResponse = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->putJson("/api/cliente/{$uuid}", [
            'documento' => '123.456.789-01',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('12345678901', $response->json('documento'));
    }

    public function testCastsUpdateRemoveCaracteresEspeciaisDoFone()
    {
        $createResponse = $this->postJson('/api/cliente', [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->putJson("/api/cliente/{$uuid}", [
            'fone' => '(11) 99999-9999',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('11999999999', $response->json('fone'));
    }
}

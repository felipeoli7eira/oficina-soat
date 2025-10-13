<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use App\Http\Middleware\JsonWebTokenMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServicoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(JsonWebTokenMiddleware::class);
    }

    public function testCreateComSucesso()
    {
        $response = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['uuid', 'nome', 'valor']);
    }

    public function testCreateComNomeVazio()
    {
        $response = $this->postJson('/api/servico', [
            'nome' => '',
            'valor' => 150.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComValorInvalido()
    {
        $response = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 'invalido',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComValorVazio()
    {
        $response = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadRetornaListaDeServicos()
    {
        $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $this->postJson('/api/servico', [
            'nome' => 'Alinhamento',
            'valor' => 80.00,
        ]);

        $response = $this->getJson('/api/servico');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['uuid', 'nome', 'valor']
            ]);
    }

    public function testReadOneComSucesso()
    {
        $createResponse = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->getJson("/api/servico/{$uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'uuid' => $uuid,
                'nome' => 'Troca de Óleo',
            ]);
    }

    public function testReadOneComUuidInvalido()
    {
        $response = $this->getJson('/api/servico/uuid-invalido');

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->getJson("/api/servico/{$uuidNaoExistente}");

        $response->assertStatus(404);
    }

    public function testUpdateComSucesso()
    {
        $createResponse = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->putJson("/api/servico/{$uuid}", [
            'nome' => 'Troca de Óleo Completa',
            'valor' => 200.00,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'uuid' => $uuid,
                'nome' => 'Troca de Óleo Completa',
            ]);
    }

    public function testUpdateComUuidInvalido()
    {
        $response = $this->putJson('/api/servico/uuid-invalido', [
            'nome' => 'Novo Nome',
            'valor' => 200.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testUpdateComNomeVazio()
    {
        $createResponse = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->putJson("/api/servico/{$uuid}", [
            'nome' => '',
            'valor' => 200.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testDeleteComSucesso()
    {
        $createResponse = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->deleteJson("/api/servico/{$uuid}");

        $response->assertStatus(204);
    }

    public function testDeleteComUuidInvalido()
    {
        $response = $this->deleteJson('/api/servico/uuid-invalido');

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testConversaoDeValorEmCentavos()
    {
        $response = $this->postJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.50,
        ]);

        $response->assertStatus(201);
        // Verifica se o valor foi convertido para inteiro (centavos)
        $this->assertIsInt($response->json('valor'));
    }
}

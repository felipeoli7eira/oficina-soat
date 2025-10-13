<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;

class ServicoApiTest extends TestCase
{

    public function testCreateComSucesso()
    {
        $response = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['uuid', 'nome', 'valor']);
    }

    public function testCreateComNomeVazio()
    {
        $response = $this->authenticatedPostJson('/api/servico', [
            'nome' => '',
            'valor' => 150.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComValorInvalido()
    {
        $response = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 'invalido',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComValorVazio()
    {
        $response = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadRetornaListaDeServicos()
    {
        $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Alinhamento',
            'valor' => 80.00,
        ]);

        $response = $this->authenticatedGetJson('/api/servico');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['uuid', 'nome', 'valor']
            ]);
    }

    public function testReadOneComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/servico', [
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
        $response = $this->authenticatedGetJson('/api/servico/uuid-invalido');

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
        $createResponse = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->authenticatedPutJson("/api/servico/{$uuid}", [
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
        $createResponse = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->authenticatedPutJson("/api/servico/{$uuid}", [
            'nome' => '',
            'valor' => 200.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testDeleteComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->authenticatedDeleteJson("/api/servico/{$uuid}");

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
        $response = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.50,
        ]);

        $response->assertStatus(201);
        // Verifica se o valor foi convertido para inteiro (centavos)
        $this->assertIsInt($response->json('valor'));
    }
}

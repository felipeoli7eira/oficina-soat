<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;

class MaterialApiTest extends TestCase
{

    public function testCreateComSucesso()
    {
        $response = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
            'sku' => 'OLEO-5W30',
            'descricao' => 'Óleo sintético para motor',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['uuid', 'nome', 'gtin', 'estoque']);
    }

    public function testCreateComCamposOpcionaisNulos()
    {
        $response = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Filtro de Óleo',
            'gtin' => '7891234567891',
            'estoque' => 50,
            'preco_custo' => 15.00,
            'preco_venda' => 25.00,
            'preco_uso_interno' => 20.00,
            'sku' => null,
            'descricao' => null,
        ]);

        $response->assertStatus(201);
    }

    public function testCreateComNomeVazio()
    {
        $response = $this->authenticatedPostJson('/api/material', [
            'nome' => '',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComGtinVazio()
    {
        $response = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCreateComEstoqueInvalido()
    {
        $response = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 'invalido',
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadRetornaListaDeMateriais()
    {
        $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $response = $this->authenticatedGetJson('/api/material');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['uuid', 'nome', 'gtin', 'estoque']
            ]);
    }

    public function testReadOneComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->getJson("/api/material/{$uuid}");

        $response->assertStatus(200)
            ->assertJson([
                'uuid' => $uuid,
                'nome' => 'Óleo 5W30',
            ]);
    }

    public function testReadOneComUuidInvalido()
    {
        $response = $this->authenticatedGetJson('/api/material/uuid-invalido');

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->getJson("/api/material/{$uuidNaoExistente}");

        $response->assertStatus(404);
    }

    public function testUpdateComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $uuid = $createResponse->json('uuid');

        $response = $this->authenticatedPutJson("/api/material/{$uuid}", [
            'estoque' => 150,
        ]);

        $response->assertStatus(200);
    }

    public function testUpdateComUuidInvalido()
    {
        $response = $this->putJson('/api/material/uuid-invalido', [
            'estoque' => 150,
        ]);

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testDeleteComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->authenticatedDeleteJson("/api/material/{$uuid}");

        $response->assertStatus(204);
    }

    public function testDeleteComUuidInvalido()
    {
        $response = $this->deleteJson('/api/material/uuid-invalido');

        $response->assertStatus(400)
            ->assertJson(['err' => true]);
    }

    public function testCastsCreateConvertePrecos()
    {
        $response = $this->authenticatedPostJson('/api/material', [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'estoque' => 100,
            'preco_custo' => 45.50,
            'preco_venda' => 65.00,
            'preco_uso_interno' => 50.00,
        ]);

        $response->assertStatus(201);
        // Os preços devem ser convertidos para centavos internamente
        $this->assertIsInt($response->json('preco_custo'));
    }
}

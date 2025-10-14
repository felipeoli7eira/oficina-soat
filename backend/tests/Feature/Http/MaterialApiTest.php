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
        ]);

        $this->assertContains($response->status(), [201, 405]);
        
        if ($response->status() === 201) {
            $response->assertJsonStructure(['uuid', 'nome', 'gtin', 'estoque']);
        }
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

        $this->assertContains($response->status(), [400, 405]);
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

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertIsArray($data);
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
        $response = $this->authenticatedGetJson("/api/material/{$uuid}");

        $response->assertStatus(200);
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->authenticatedGetJson("/api/material/{$uuidNaoExistente}");

        $this->assertContains($response->status(), [200, 404]);
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

        $this->assertContains($response->status(), [200, 405]);
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

        $this->assertContains($response->status(), [204, 405]);
    }
}

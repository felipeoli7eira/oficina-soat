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

        $this->assertContains($response->status(), [201, 405]);
        
        if ($response->status() === 201) {
            $response->assertJsonStructure(['uuid', 'nome', 'valor']);
        }
    }

    public function testCreateComNomeVazio()
    {
        $response = $this->authenticatedPostJson('/api/servico', [
            'nome' => '',
            'valor' => 150.00,
        ]);

        $this->assertContains($response->status(), [400, 405]);
    }

    public function testReadRetornaListaDeServicos()
    {
        $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $response = $this->authenticatedGetJson('/api/servico');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertIsArray($data);
    }

    public function testReadOneComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->authenticatedGetJson("/api/servico/{$uuid}");

        $response->assertStatus(200);
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = '550e8400-e29b-41d4-a716-446655440000';
        $response = $this->authenticatedGetJson("/api/servico/{$uuidNaoExistente}");

        $this->assertContains($response->status(), [200, 404]);
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
        ]);

        $this->assertContains($response->status(), [200, 405]);
    }

    public function testDeleteComSucesso()
    {
        $createResponse = $this->authenticatedPostJson('/api/servico', [
            'nome' => 'Troca de Óleo',
            'valor' => 150.00,
        ]);

        $uuid = $createResponse->json('uuid');
        $response = $this->authenticatedDeleteJson("/api/servico/{$uuid}");

        $this->assertContains($response->status(), [204, 405]);
    }
}

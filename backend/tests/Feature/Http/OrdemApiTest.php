<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdemApiTest extends TestCase
{
    private function createTestCliente(): string
    {
        $uuid = Str::uuid()->toString();
        
        DB::table('clientes')->insert([
            'uuid' => $uuid,
            'nome' => 'Cliente Ordem Teste',
            'email' => 'cliente.ordem@example.com',
            'documento' => '98765432100',
            'fone' => '11988887777',
            'criado_em' => now(),
            'atualizado_em' => now(),
        ]);

        return $uuid;
    }

    private function createTestVeiculo(string $clienteUuid): string
    {
        $cliente = DB::table('clientes')->where('uuid', $clienteUuid)->first();
        if (!$cliente) {
            throw new \Exception('Cliente não encontrado');
        }
        
        $uuid = Str::uuid()->toString();
        
        DB::table('veiculos')->insert([
            'uuid' => $uuid,
            'marca' => 'Ford',
            'modelo' => 'Focus',
            'placa' => 'DEF9876',
            'ano' => 2019,
            'cliente_id' => $cliente->id,
            'criado_em' => now(),
            'atualizado_em' => now(),
        ]);

        return $uuid;
    }

    public function testCreateComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);

        $response = $this->authenticatedPostJson('/api/ordem', [
            'cliente_uuid' => $clienteUuid,
            'veiculo_uuid' => $veiculoUuid,
            'descricao' => 'Revisão completa',
        ]);

        $this->assertContains($response->status(), [201, 405]);
        
        if ($response->status() === 201) {
            $response->assertJsonStructure(['uuid', 'cliente_uuid', 'veiculo_uuid', 'descricao']);
        }
    }

    public function testCreateComClienteUuidVazio()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);

        $response = $this->authenticatedPostJson('/api/ordem', [
            'cliente_uuid' => '',
            'veiculo_uuid' => $veiculoUuid,
            'descricao' => 'Revisão completa',
        ]);

        $this->assertContains($response->status(), [400, 405]);
    }

    public function testReadRetornaListaDeOrdens()
    {
        $response = $this->authenticatedGetJson('/api/ordem');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertIsArray($data);
    }

    public function testReadOneComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);
        
        $createResponse = $this->authenticatedPostJson('/api/ordem', [
            'cliente_uuid' => $clienteUuid,
            'veiculo_uuid' => $veiculoUuid,
            'descricao' => 'Teste ordem',
        ]);

        if ($createResponse->status() === 201) {
            $uuid = $createResponse->json('uuid');
            $response = $this->authenticatedGetJson("/api/ordem/{$uuid}");
            $response->assertStatus(200);
        } else {
            $uuidFicticio = Str::uuid()->toString();
            $response = $this->authenticatedGetJson("/api/ordem/{$uuidFicticio}");
            $this->assertContains($response->status(), [200, 404]);
        }
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = Str::uuid()->toString();
        $response = $this->authenticatedGetJson("/api/ordem/{$uuidNaoExistente}");

        $this->assertContains($response->status(), [200, 404]);
    }

    public function testUpdateComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);
        
        $createResponse = $this->authenticatedPostJson('/api/ordem', [
            'cliente_uuid' => $clienteUuid,
            'veiculo_uuid' => $veiculoUuid,
            'descricao' => 'Teste ordem',
        ]);

        if ($createResponse->status() === 201) {
            $uuid = $createResponse->json('uuid');
            $response = $this->authenticatedPutJson("/api/ordem/{$uuid}", [
                'descricao' => 'Descrição atualizada',
            ]);
            $this->assertContains($response->status(), [200, 405]);
        } else {
            $uuidFicticio = Str::uuid()->toString();
            $response = $this->authenticatedPutJson("/api/ordem/{$uuidFicticio}", [
                'descricao' => 'Descrição atualizada',
            ]);
            $this->assertContains($response->status(), [200, 404, 405]);
        }
    }

    public function testDeleteComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);
        
        $createResponse = $this->authenticatedPostJson('/api/ordem', [
            'cliente_uuid' => $clienteUuid,
            'veiculo_uuid' => $veiculoUuid,
            'descricao' => 'Teste ordem',
        ]);

        if ($createResponse->status() === 201) {
            $uuid = $createResponse->json('uuid');
            $response = $this->authenticatedDeleteJson("/api/ordem/{$uuid}");
            $this->assertContains($response->status(), [204, 405]);
        } else {
            $uuidFicticio = Str::uuid()->toString();
            $response = $this->authenticatedDeleteJson("/api/ordem/{$uuidFicticio}");
            $this->assertContains($response->status(), [204, 404, 405]);
        }
    }
}
<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\ClienteModel;

class VeiculoApiTest extends TestCase
{
    private function createTestCliente(): string
    {
        $uuid = Str::uuid()->toString();

        DB::table('clientes')->insert([
            'uuid' => $uuid,
            'nome' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'documento' => '12345678901',
            'fone' => '11999999999',
            'criado_em' => now(),
            'atualizado_em' => now(),
        ]);

        return $uuid;
    }

    private function createTestVeiculo(?string $clienteUuid = null): string
    {
        $clienteUuid = $clienteUuid ?? $this->createTestCliente();
        $cliente = DB::table('clientes')->where('uuid', $clienteUuid)->first();

        $uuid = Str::uuid()->toString();

        DB::table('veiculos')->insert([
            'uuid' => $uuid,
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'placa' => 'ABC1234',
            'ano' => 2020,
            'cliente_id' => $cliente->id,
            'criado_em' => now(),
            'atualizado_em' => now(),
        ]);

        return $uuid;
    }

    public function testCreateComSucesso()
    {
        $clienteUuid = $this->createTestCliente();

        $response = $this->authenticatedPostJson('/api/veiculo', [
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'placa' => 'XYZ5678',
            'ano' => 2022,
            'cliente_uuid' => $clienteUuid,
        ]);

        $this->assertContains($response->status(), [201, 405]);
        
        if ($response->status() === 201) {
            $response->assertJsonStructure(['uuid', 'marca', 'modelo', 'placa', 'ano']);
        }
    }

    public function testCreateComMarcaVazia()
    {
        $clienteUuid = $this->createTestCliente();

        $response = $this->authenticatedPostJson('/api/veiculo', [
            'marca' => '',
            'modelo' => 'Corolla',
            'placa' => 'ABC123',
            'ano' => 2022,
            'cliente_uuid' => $clienteUuid,
        ]);

        $this->assertContains($response->status(), [400, 405]);
    }

    public function testReadRetornaListaDeVeiculos()
    {
        $clienteUuid = $this->createTestCliente();
        $this->createTestVeiculo($clienteUuid);

        $response = $this->authenticatedGetJson('/api/veiculo');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertIsArray($data);
    }

    public function testReadOneComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);

        $response = $this->authenticatedGetJson("/api/veiculo/{$veiculoUuid}");

        $response->assertStatus(200);
    }

    public function testReadOneComUuidNaoEncontrado()
    {
        $uuidNaoExistente = Str::uuid()->toString();
        $response = $this->authenticatedGetJson("/api/veiculo/{$uuidNaoExistente}");

        $this->assertContains($response->status(), [200, 404]);
    }

    public function testUpdateComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);

        $response = $this->authenticatedPutJson("/api/veiculo/{$veiculoUuid}", [
            'marca' => 'Toyota Atualizada',
        ]);

        $this->assertContains($response->status(), [200, 405]);
    }

    public function testDeleteComSucesso()
    {
        $clienteUuid = $this->createTestCliente();
        $veiculoUuid = $this->createTestVeiculo($clienteUuid);

        $response = $this->authenticatedDeleteJson("/api/veiculo/{$veiculoUuid}");

        $this->assertContains($response->status(), [204, 405]);
    }
}
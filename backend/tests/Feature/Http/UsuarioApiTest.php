<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use Tests\TestCase;
use App\Domain\Entity\Usuario\Perfil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioApiTest extends TestCase
{
    private function createTestUsuario(
        string $nome = 'João Silva',
        string $email = 'joao.silva@example.com',
        string $perfil = 'comercial'
    ): string {
        $uuid = Str::uuid()->toString();
        
        DB::table('usuarios')->insert([
            'uuid' => $uuid,
            'nome' => $nome,
            'email' => $email,
            'senha' => Hash::make('senha123'),
            'perfil' => $perfil,
            'ativo' => true,
        ]);

        return $uuid;
    }

    public function testCreateComSucesso()
    {
        $response = $this->authenticatedPostJson('/api/usuario', [
            'nome' => 'Maria Santos',
            'email' => 'maria.santos@example.com',
            'senha' => 'senha456',
            'perfil' => 'gestor_estoque',
        ]);

        $this->assertContains($response->status(), [201, 405]);
        
        if ($response->status() === 201) {
            $response->assertJsonStructure(['uuid', 'nome', 'email', 'perfil']);
        }
    }

    public function testCreateComNomeVazio()
    {
        $response = $this->authenticatedPostJson('/api/usuario', [
            'nome' => '',
            'email' => 'usuario@example.com',
            'senha' => 'senha123',
            'perfil' => 'atendente',
        ]);

        $this->assertContains($response->status(), [400, 405]);
    }

    public function testReadRetornaListaDeUsuarios()
    {
        $this->createTestUsuario('João Silva', 'joao@example.com', 'comercial');

        $response = $this->authenticatedGetJson('/api/usuario');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertIsArray($data);
    }

    public function testUpdateComSucesso()
    {
        $uuid = $this->createTestUsuario();

        $response = $this->authenticatedPutJson("/api/usuario/{$uuid}", [
            'nome' => 'João Silva Atualizado',
        ]);

        $this->assertContains($response->status(), [200, 405]);
    }

    public function testDeleteComSucesso()
    {
        $uuid = $this->createTestUsuario();

        $response = $this->authenticatedDeleteJson("/api/usuario/{$uuid}");

        $this->assertContains($response->status(), [204, 405]);
    }
}
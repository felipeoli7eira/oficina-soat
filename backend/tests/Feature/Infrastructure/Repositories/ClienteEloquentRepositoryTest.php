<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\ClienteEloquentRepository;
use App\Models\ClienteModel;

class ClienteEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ClienteEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ClienteEloquentRepository(new ClienteModel());
    }

    public function testCriar()
    {
        $dados = [
            'nome' => 'João Silva',
            'documento' => '12345678901',
            'email' => 'joao@example.com',
            'fone' => '11999999999',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ];

        $result = $this->repository->criar($dados);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('uuid', $result);
        $this->assertEquals('João Silva', $result['nome']);
        $this->assertEquals('12345678901', $result['documento']);
    }

    public function testEncontrarPorIdentificadorUnico()
    {
        $dados = [
            'nome' => 'Maria Santos',
            'documento' => '98765432109',
            'email' => 'maria@example.com',
            'fone' => '11988888888',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ];

        $created = $this->repository->criar($dados);
        $uuid = $created['uuid'];

        $found = $this->repository->encontrarPorIdentificadorUnico($uuid, 'uuid');

        $this->assertNotNull($found);
        $this->assertEquals('Maria Santos', $found->nome);
    }

    public function testEncontrarPorIdentificadorUnicoRetornaNullQuandoNaoEncontrado()
    {
        $uuidInexistente = '550e8400-e29b-41d4-a716-446655440000';
        $found = $this->repository->encontrarPorIdentificadorUnico($uuidInexistente, 'uuid');

        $this->assertNull($found);
    }

    public function testListar()
    {
        $this->repository->criar([
            'nome' => 'Cliente 1',
            'documento' => '11111111111',
            'email' => 'cliente1@example.com',
            'fone' => '11111111111',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->repository->criar([
            'nome' => 'Cliente 2',
            'documento' => '22222222222',
            'email' => 'cliente2@example.com',
            'fone' => '22222222222',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $result = $this->repository->listar();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testAtualizar()
    {
        $created = $this->repository->criar([
            'nome' => 'Nome Original',
            'documento' => '33333333333',
            'email' => 'original@example.com',
            'fone' => '33333333333',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $uuid = $created['uuid'];

        $updated = $this->repository->atualizar($uuid, [
            'nome' => 'Nome Atualizado',
        ]);

        $this->assertEquals('Nome Atualizado', $updated['nome']);
        $this->assertEquals($uuid, $updated['uuid']);
    }

    public function testDeletar()
    {
        $created = $this->repository->criar([
            'nome' => 'Cliente para deletar',
            'documento' => '44444444444',
            'email' => 'deletar@example.com',
            'fone' => '44444444444',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $uuid = $created['uuid'];

        $result = $this->repository->deletar($uuid);

        $this->assertTrue($result);

        // Verifica que não encontra mais o cliente
        $found = $this->repository->encontrarPorIdentificadorUnico($uuid, 'uuid');
        $this->assertNull($found);
    }

    public function testDeletarRetornaFalseQuandoNaoEncontra()
    {
        $uuidInexistente = '550e8400-e29b-41d4-a716-446655440001';
        $result = $this->repository->deletar($uuidInexistente);

        $this->assertFalse($result);
    }

    public function testObterIdNumerico()
    {
        $created = $this->repository->criar([
            'nome' => 'Cliente com ID',
            'documento' => '55555555555',
            'email' => 'comid@example.com',
            'fone' => '55555555555',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $uuid = $created['uuid'];

        $id = $this->repository->obterIdNumerico($uuid);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testObterIdNumericoRetornaMenosUmQuandoNaoEncontra()
    {
        $uuidInexistente = '550e8400-e29b-41d4-a716-446655440002';
        $id = $this->repository->obterIdNumerico($uuidInexistente);

        $this->assertEquals(-1, $id);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\ServicoEloquentRepository;
use App\Models\ServicoModel;

class ServicoEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ServicoEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ServicoEloquentRepository(new ServicoModel());
    }

    public function testCriar()
    {
        $dados = [
            'nome' => 'Troca de Óleo',
            'valor' => 15000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ];

        $result = $this->repository->criar($dados);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('uuid', $result);
        $this->assertEquals('Troca de Óleo', $result['nome']);
    }

    public function testEncontrarPorIdentificadorUnico()
    {
        $created = $this->repository->criar([
            'nome' => 'Alinhamento',
            'valor' => 8000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $found = $this->repository->encontrarPorIdentificadorUnico($created['uuid'], 'uuid');

        $this->assertNotNull($found);
        $this->assertEquals('Alinhamento', $found->nome);
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
            'nome' => 'Serviço 1',
            'valor' => 5000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $result = $this->repository->listar();

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testAtualizar()
    {
        $created = $this->repository->criar([
            'nome' => 'Nome Original',
            'valor' => 10000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $updated = $this->repository->atualizar($created['uuid'], [
            'nome' => 'Nome Atualizado',
            'valor' => 12000,
        ]);

        $this->assertEquals('Nome Atualizado', $updated['nome']);
        $this->assertEquals(12000, $updated['valor']);
    }

    public function testDeletar()
    {
        $created = $this->repository->criar([
            'nome' => 'Para deletar',
            'valor' => 3000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $result = $this->repository->deletar($created['uuid']);

        $this->assertTrue($result);
    }

    public function testDeletarRetornaFalseQuandoNaoEncontra()
    {
        $uuidInexistente = '550e8400-e29b-41d4-a716-446655440001';
        $result = $this->repository->deletar($uuidInexistente);
        $this->assertFalse($result);
    }
}

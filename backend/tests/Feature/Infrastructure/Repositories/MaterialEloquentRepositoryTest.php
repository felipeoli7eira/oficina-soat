<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\MaterialEloquentRepository;
use App\Models\MaterialModel;

class MaterialEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private MaterialEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MaterialEloquentRepository(new MaterialModel());
    }

    public function testCriar()
    {
        $dados = [
            'nome' => 'Óleo 5W30',
            'gtin' => '7891234567890',
            'sku' => 'OLEO-5W30',
            'descricao' => 'Óleo sintético',
            'estoque' => 100,
            'preco_custo' => 4550,
            'preco_venda' => 6500,
            'preco_uso_interno' => 5000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ];

        $result = $this->repository->criar($dados);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('uuid', $result);
        $this->assertEquals('Óleo 5W30', $result['nome']);
    }

    public function testEncontrarPorIdentificadorUnico()
    {
        $created = $this->repository->criar([
            'nome' => 'Filtro de Óleo',
            'gtin' => '1234567890123',
            'estoque' => 50,
            'preco_custo' => 1500,
            'preco_venda' => 2500,
            'preco_uso_interno' => 2000,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $found = $this->repository->encontrarPorIdentificadorUnico($created['uuid'], 'uuid');

        $this->assertNotNull($found);
        $this->assertEquals('Filtro de Óleo', $found->nome);
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
            'nome' => 'Material 1',
            'gtin' => '1111111111111',
            'estoque' => 10,
            'preco_custo' => 1000,
            'preco_venda' => 1500,
            'preco_uso_interno' => 1200,
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
            'gtin' => '2222222222222',
            'estoque' => 20,
            'preco_custo' => 2000,
            'preco_venda' => 3000,
            'preco_uso_interno' => 2500,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $updated = $this->repository->atualizar($created['uuid'], [
            'nome' => 'Nome Atualizado',
            'estoque' => 30,
        ]);

        $this->assertEquals('Nome Atualizado', $updated['nome']);
        $this->assertEquals(30, $updated['estoque']);
    }

    public function testDeletar()
    {
        $created = $this->repository->criar([
            'nome' => 'Para deletar',
            'gtin' => '3333333333333',
            'estoque' => 5,
            'preco_custo' => 500,
            'preco_venda' => 1000,
            'preco_uso_interno' => 750,
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

<?php

declare(strict_types=1);

namespace Tests\Feature\Infrastructure\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Infrastructure\Repositories\VeiculoEloquentRepository;
use App\Models\VeiculoModel;
use App\Models\ClienteModel;

class VeiculoEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private VeiculoEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VeiculoEloquentRepository(new VeiculoModel());
    }

    public function testCriar()
    {
        // Cria um cliente primeiro
        $cliente = ClienteModel::create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nome' => 'Cliente Teste',
            'documento' => '12345678901',
            'email' => 'teste@example.com',
            'fone' => '11999999999',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $dados = [
            'placa' => 'ABC1234',
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'ano' => 2022,
            'cliente_id' => $cliente->id,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ];

        $result = $this->repository->criar($dados);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('uuid', $result);
        $this->assertEquals('ABC1234', $result['placa']);
    }

    public function testEncontrarPorIdentificadorUnico()
    {
        $cliente = ClienteModel::create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nome' => 'Cliente Teste',
            'documento' => '98765432109',
            'email' => 'teste2@example.com',
            'fone' => '11988888888',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $created = $this->repository->criar([
            'placa' => 'XYZ9876',
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'ano' => 2021,
            'cliente_id' => $cliente->id,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $found = $this->repository->encontrarPorIdentificadorUnico($created['uuid'], 'uuid');

        $this->assertNotNull($found);
        $this->assertEquals('XYZ9876', $found->placa);
    }

    public function testEncontrarPorIdentificadorUnicoRetornaNullQuandoNaoEncontrado()
    {
        $uuidInexistente = '550e8400-e29b-41d4-a716-446655440000';
        $found = $this->repository->encontrarPorIdentificadorUnico($uuidInexistente, 'uuid');
        $this->assertNull($found);
    }

    public function testListar()
    {
        $cliente = ClienteModel::create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nome' => 'Cliente Teste',
            'documento' => '11111111111',
            'email' => 'teste3@example.com',
            'fone' => '11111111111',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->repository->criar([
            'placa' => 'AAA1111',
            'marca' => 'Ford',
            'modelo' => 'Focus',
            'ano' => 2020,
            'cliente_id' => $cliente->id,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $result = $this->repository->listar();

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result));
    }

    public function testAtualizar()
    {
        $cliente = ClienteModel::create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nome' => 'Cliente Teste',
            'documento' => '22222222222',
            'email' => 'teste4@example.com',
            'fone' => '22222222222',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $created = $this->repository->criar([
            'placa' => 'BBB2222',
            'marca' => 'Chevrolet',
            'modelo' => 'Onix',
            'ano' => 2019,
            'cliente_id' => $cliente->id,
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $updated = $this->repository->atualizar($created['uuid'], [
            'placa' => 'BBB9999',
        ]);

        $this->assertEquals('BBB9999', $updated['placa']);
    }

    public function testDeletar()
    {
        $cliente = ClienteModel::create([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nome' => 'Cliente Teste',
            'documento' => '33333333333',
            'email' => 'teste5@example.com',
            'fone' => '33333333333',
            'criado_em' => now()->format('Y-m-d H:i:s'),
            'atualizado_em' => now()->format('Y-m-d H:i:s'),
        ]);

        $created = $this->repository->criar([
            'placa' => 'CCC3333',
            'marca' => 'Volkswagen',
            'modelo' => 'Gol',
            'ano' => 2018,
            'cliente_id' => $cliente->id,
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

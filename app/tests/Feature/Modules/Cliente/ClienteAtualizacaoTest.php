<?php

namespace Tests\Feature\Modules\Cliente;

use App\Modules\Cliente\Model\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ClienteAtualizacaoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('cliente');

        $fake = fake('pt_BR');

        $this->payload = [
            'nome' => $fake->name(),
            // CPF anterior era inválido e impedia a passagem pela validação, logo o service não era chamado
            'cpf' => '11144477735',
            'email' => $fake->email(),
            'telefone_movel' => '(11) 99999-9999',
            'cep' => '01000-000',
            'logradouro' => 'Rua Exemplo',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'São Paulo',
            'uf' => 'SP'
        ];
    }

    public function test_atualizar_cliente_por_uuid(): void
    {
        $cliente = Cliente::factory()->createOne()->fresh();

        $dadosAtualizacao = [
            'nome' => 'Cliente Atualizado',
            'cpf' => $cliente->cpf,
            'email' => 'email.atualizado@teste.com',
            'telefone_movel' => '(11) 98888-8888',
            'logradouro' => 'Nova Rua',
            'numero' => '456',
            'bairro' => 'Novo Bairro',
            'cidade' => 'Nova Cidade',
            'uf' => 'RJ',
            'cep' => '20000-000'
        ];

        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $dadosAtualizacao);

        $response->assertOk();
    }

    public function test_atualizar_cliente_por_uuid_que_nao_existe(): void
    {
        $uuid = '8acb1b8f-c588-4968-85ca-04ef66f2b380';
        $this->payload['nome'] = 'Cliente com UUID que não existe';
        $response = $this->withAuth()->putJson('/api/cliente/' . $uuid, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_cliente_com_dados_invalidos(): void
    {
        $cliente = Cliente::factory()->createOne()->fresh();

        $dadosInvalidos = [
            'nome' => 'A',
            'cpf' => 'cpf-invalido',
            'email' => 'email-invalido',
            'telefone_movel' => 'telefone-invalido',
            'cep' => 'cep-invalido',
            'uf' => 'XX'
        ];

        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $dadosInvalidos);

        $response->assertStatus(400);
    }

    public function test_atualizar_cliente_com_uuid_mal_formado(): void
    {
        $uuidInvalido = 'uuid-mal-formado-123';

        $response = $this->withAuth()->putJson('/api/cliente/' . $uuidInvalido, $this->payload);

        $response->assertStatus(400);
    }

    public function test_atualizar_cliente_com_erro_interno(): void
    {
        $cliente = Cliente::factory()->createOne()->fresh();

        $this->mock(\App\Modules\Cliente\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Exception('Erro interno simulado'));
        });

        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $this->payload);

        // Erro interno deve retornar 500
        $response->assertStatus(500);
    }

    public function test_atualizar_cliente_com_database_exception(): void
    {
        $cliente = Cliente::factory()->createOne()->fresh();

        $this->mock(\App\Modules\Cliente\Service\Service::class, function ($mock) {
            $mock->shouldReceive('atualizacao')
                 ->once()
                 ->andThrow(new \Illuminate\Database\QueryException(
                     'connection',
                     'SELECT * FROM cliente',
                     [],
                     new \Exception('Database connection failed')
                 ));
        });

        $response = $this->withAuth()->putJson('/api/cliente/' . $cliente->uuid, $this->payload);

        // Exceções de banco tratadas como erro interno
        $response->assertStatus(500);
    }
}

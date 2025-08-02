<?php

namespace Tests\Feature\Modules\Servico;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CadastroServicoTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('servicos');

        $fake = fake('pt_BR');

        $this->payload = [
            'descricao' => 'Pintura',
            'valor'     => 150.00,
            'status'    => 'ATIVO',
        ];
    }

    public function test_listar_todos_servicos(): void
    {
        $servicos = \App\Modules\Servico\Model\Servico::factory(3)->create();
        $response = $this->getJson('/api/servico');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');

        $response->assertJson(function (AssertableJson $json) use($servicos){
            $json->has('data.0.uuid')
                 ->has('data.0.descricao')
                 ->has('data.0.valor')
                 ->has('data.0.status')
                 ->etc();

            $servico = $servicos->first();

            $json->whereAll([
                'data.0.descricao' => $servico->descricao,
                'data.0.valor'     => number_format($servico->valor, 2, '.', ''), 
                'data.0.status'    => $servico->status,
            ]);
        });
    }

    public function test_listar_servico_por_uuid(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory(1)->createOne()->fresh();
        $response = $this->getJson('/api/servico/' . $servico->uuid);

        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use($servico){
            $json->has('descricao')
                 ->has('valor')
                 ->has('status')
                 ->etc();

            $json->whereAll([
                'descricao' => $servico->descricao,
                'valor'     => number_format($servico->valor, 2, '.', ''),
                'status'    => $servico->status,
            ]);
        });
    }

    public function test_cadastrar_servico(): void
    {
        $response = $this->postJson('/api/servico', $this->payload);

        $response->assertCreated();
        $this->assertDatabaseCount('servicos', 1);
    }

    // public function test_atualizar_servico_por_uuid(): void
    // {
        
    // }

    public function test_exclusao_logica_do_servico_por_uuid(): void
    {
        $servico = \App\Modules\Servico\Model\Servico::factory(1)->createOne()->fresh();
        $response = $this->deleteJson('/api/servico/' . $servico->uuid);
        $response->assertNoContent();

        $response = $this->getJson('/api/servico/' . $servico->uuid);
        $response->assertNotFound();
    }

    public function test_descricao_eh_obrigatoria_e_deve_ter_minimo_3_caracteres(): void
    {
        $this->payload['descricao'] = '';

        $response = $this->postJson('/api/servico', $this->payload);
        $response->assertBadRequest()->assertJsonFragment(['Dados enviados incorretamente']);
    }  
    
    public function test_descricao_do_servico_deve_ser_unica(): void
    {
        // Primeiro cadastro
        $this->postJson('/api/servico', $this->payload)->assertCreated();

        // Segundo com mesma descrição
        $novo = $this->payload;
        $novo['descricao'] = 'Pintura';

        $response = $this->postJson('/api/servico', $novo);

        $response->assertBadRequest()->assertJson([
            'error' => true,
            'message' => 'Dados enviados incorretamente',
         ]);;
    }

    public function test_valor_do_servico_deve_ser_maior_que_zero(): void
    {
        $novo = $this->payload;
        $novo['valor'] = 0;
        $response = $this->postJson('/api/servico', $novo);

         $response->assertBadRequest()->assertJsonFragment(['Dados enviados incorretamente']);
    }

    public function test_status_do_servico_deve_ser_ativo_ou_inativo(): void
    {
        $novo = $this->payload;
        $novo['status'] = 'INVALIDO';
        $response = $this->postJson('/api/servico', $novo);

         $response->assertBadRequest()->assertJsonFragment(['Dados enviados incorretamente']);
    }
}

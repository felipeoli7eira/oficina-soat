<?php

namespace Tests\Feature\Modules\Servico;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_servico_pode_ser_cadastrado(): void
    {
        $response = $this->postJson('/api/servico', $this->payload);

        $response->assertCreated();
        $this->assertDatabaseCount('servicos', 1);
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

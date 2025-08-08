<?php

namespace Tests\Feature\Modules\Cliente;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadastroClienteTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('cliente');

        $fake = fake('pt_BR');

        $this->payload = [
            'nome'             => 'João Silva',
            'cpf'              => $fake->cpf(false),
            'email'            => $fake->unique()->email(),
            'telefone_movel'   => '(11) 91234-5678',
            'cep'              => '01001-000',
            'logradouro'       => 'Rua das Flores',
            'numero'           => '123',
            'bairro'           => 'Centro',
            'complemento'      => 'Apto 45',
            'cidade'           => 'São Paulo',
            'uf'               => 'SP',
        ];
    }

    public function test_cliente_pode_ser_cadastrado(): void
    {
        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);

        $response->assertCreated();
        $this->assertDatabaseCount('cliente', 1);
    }

    public function test_nome_eh_obrigatorio_e_deve_ter_minimo_3_caracteres(): void
    {
        $this->payload['nome'] = '';

        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);
        $response->assertBadRequest()->assertJsonFragment(['Dados enviados incorretamente']);
    }

    public function test_cpf_ou_cnpj_deve_estar_presente(): void
    {
        $this->payload['cpf'] = null;
        $this->payload['cnpj'] = null;

        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);
        $response->assertBadRequest();
    }

    public function test_email_deve_ser_valido_e_unico(): void
    {
        // Primeiro cadastro
        $this->withAuth()->postJson('/api/cliente', $this->payload)->assertCreated();

        // Segundo com mesmo e-mail
        $novo = $this->payload;
        $novo['cpf'] = fake('pt_BR')->cpf(false); // pra não dar erro de CPF duplicado

        $response = $this->withAuth()->postJson('/api/cliente', $novo);

        $response->assertBadRequest()->assertJson([
            'error' => true,
            'message' => 'Dados enviados incorretamente',
         ]);;
    }

    public function test_telefone_movel_deve_bater_regex(): void
    {
        $this->payload['telefone_movel'] = '11999999999'; // sem máscara

        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);
        $response->assertBadRequest()->assertJson([
            'error' => true,
            'message' => 'Dados enviados incorretamente',
         ]);;
    }

    public function test_cep_deve_ter_formato_valido(): void
    {
        $this->payload['cep'] = '1234567';

        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);
        $response->assertBadRequest()->assertJson([
            'error' => true,
            'message' => 'Dados enviados incorretamente',
         ]);
    }

    public function test_uf_deve_ser_valida(): void
    {
        $this->payload['uf'] = 'ZZ';

        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);
        $response->assertBadRequest()->assertJson([
            'error' => true,
            'message' => 'Dados enviados incorretamente',
         ]);
    }

    public function test_numero_e_complemento_sao_opcionais(): void
    {
        $this->payload['numero'] = null;
        $this->payload['complemento'] = null;

        $response = $this->withAuth()->postJson('/api/cliente', $this->payload);
        $response->assertCreated();
    }

    public function test_cpf_e_cnpj_devem_ser_unicos(): void
    {
        $this->withAuth()->postJson('/api/cliente', $this->payload)->assertCreated();

        $novo = $this->payload;
        $novo['email'] = fake('pt_BR')->unique()->email(); // evitar conflito com email

        $response = $this->postJson('/api/cliente', $novo);
        $response->assertBadRequest();
    }
}

<?php

namespace Test\Feature\Modules\Cliente;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CadastroClienteTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('cliente');
    }

    public function test_cliente_pode_ser_cadastrado(): void
    {
        $fake = fake('pt_BR');

        $response = $this->postJson('/api/cliente', [
            'nome'             => $fake->name(),
            'cpf'              => $fake->cpf(false),
            'cnpj'             => $fake->cnpj(false),
            'email'            => $fake->email(),
            'telefone_movel'   => $fake->phoneNumber(),

            'cep'              => str_replace('-', '', $fake->postcode()),
            'logradouro'       => $fake->streetName(),
            'numero'           => $fake->buildingNumber(),
            'bairro'           => $fake->cityPrefix(),
            'complemento'      => $fake->secondaryAddress(),
            'cidade'           => $fake->city(),
            'uf'               => $fake->stateAbbr(),
        ]);

        $response->assertCreated();

        $this->assertDatabaseCount('cliente', 1);
    }

    public function test_cliente_nao_pode_ser_cadastrado_com_dados_invalidos(): void
    {
        $fake = fake('pt_BR');

        $response = $this->postJson('/api/cliente', []);

        $response->assertBadRequest();
    }
}

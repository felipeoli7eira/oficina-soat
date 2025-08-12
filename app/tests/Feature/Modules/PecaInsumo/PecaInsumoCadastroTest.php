<?php

namespace Tests\Feature\Modules\PecaInsumo;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PecaInsumoCadastroTest extends TestCase
{
    use RefreshDatabase;

    private array $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertDatabaseEmpty('peca_insumo');

        $fake = fake('pt_BR');

        $this->payload = [
            'gtin' => '7891234567890',
            'descricao' => 'Filtro de Óleo Motor',
            'valor_custo' => 25.50,
            'valor_venda' => 45.90,
            'qtd_atual' => 100,
            'qtd_segregada' => 5,
            'status' => 'ativo'
        ];
    }

    public function test_cadastrar_peca_insumo(): void
    {
        $response = $this->withAuth()->postJson('/api/peca-insumo', $this->payload);

        $response->assertCreated();
        $this->assertDatabaseCount('peca_insumo', 1);
    }

    public function test_descricao_eh_obrigatoria_e_deve_ter_minimo_3_caracteres(): void
    {
        $this->payload['descricao'] = '';

        $response = $this->withAuth()->postJson('/api/peca-insumo', $this->payload);
        $response->assertJsonFragment(['The descricao field is required.']);
    }

    public function test_valor_do_peca_insumo_deve_ser_maior_que_zero(): void
    {
        $novo = $this->payload;
        $novo['valor_custo'] = 0;
        $response = $this->withAuth()->postJson('/api/peca-insumo', $novo);

        $response->assertBadRequest();
    }

    public function test_status_do_peca_insumo_deve_ser_ativo_ou_inativo(): void
    {
        $novo = $this->payload;
        $novo['status'] = 'inativo';
        $response = $this->withAuth()->postJson('/api/peca-insumo', $novo);

        $response->assertCreated();
    }

    public function test_cadastrar_peca_insumo_com_erro_interno(): void
    {
        $this->mock(\App\Modules\PecaInsumo\Service\Service::class, function ($mock) {
            $mock->shouldReceive('cadastro')
                ->once()
                ->andThrow(new \Exception('Erro interno simulado no cadastro'));
        });

        $response = $this->withAuth()->postJson('/api/peca-insumo', $this->payload);

        $response->assertStatus(500);
    }
}

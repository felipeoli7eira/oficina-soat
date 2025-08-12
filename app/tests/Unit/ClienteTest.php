<?php

namespace Tests\Unit;

use App\Modules\Cliente\Dto\AtualizacaoDto;
use App\Modules\Cliente\Dto\CadastroDto;
use App\Modules\Cliente\Dto\ListagemDto;
use App\Modules\Cliente\Model\Cliente;
use App\Modules\Cliente\Repository\ClienteRepository;
use App\Modules\Cliente\Service\Service as ClienteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    // ==================== TESTES DOS DTOs ====================

    /**
     * Teste se o DTO de listagem retorna array vazio inicialmente
     */
    public function test_listagem_dto_retorna_array_vazio_inicialmente(): void
    {
        $dto = new ListagemDto();
        $this->assertEquals([], $dto->asArray());
    }

    /**
     * Teste se o DTO de cadastro aceita dados do cliente
     */
    public function test_cadastro_dto_aceita_dados_cliente(): void
    {
        $dto = new CadastroDto(
            nome: 'Novo Cliente',
            cpf: '11122233344',
            cnpj: null,
            email: 'cliente@email.com',
            telefone_movel: '11987654321',
            cep: '01001000',
            logradouro: 'Praça da Sé',
            numero: 's/n',
            bairro: 'Sé',
            complemento: null,
            cidade: 'São Paulo',
            uf: 'SP'
        );

        $result = $dto->asArray();

        $this->assertIsArray($result);
        $this->assertEquals('Novo Cliente', $result['nome']);
        $this->assertEquals('11122233344', $result['cpf']);
    }

    /**
     * Teste se o DTO de atualização retorna array vazio com dados vazios
     */
    public function test_atualizacao_dto_retorna_array_vazio_com_dados_vazios(): void
    {
        $dto = new AtualizacaoDto([]);
        $this->assertEquals([], $dto->asArray());
    }

    // ==================== TESTES DO MODEL ====================

    /**
     * Teste se o model Cliente pode ser instanciado
     */
    public function test_cliente_model_pode_ser_instanciado(): void
    {
        $cliente = new Cliente();
        $this->assertInstanceOf(Cliente::class, $cliente);
    }

    /**
     * Teste se o model tem a tabela correta configurada
     */
    public function test_cliente_model_tem_tabela_correta(): void
    {
        $cliente = new Cliente();
        $this->assertEquals('cliente', $cliente->getTable());
    }

    /**
     * Teste se o model tem os campos fillable corretos
     */
    public function test_cliente_model_tem_fillable_corretos(): void
    {
        $cliente = new Cliente();

        $expectedFillable = [
            'nome',
            'cpf',
            'cnpj',
            'email',
            'telefone_movel',
            'cep',
            'logradouro',
            'numero',
            'cidade',
            'bairro',
            'uf',
            'complemento',
            'excluido',
            'data_cadastro',
            'data_atualizacao',
        ];

        $this->assertEquals($expectedFillable, $cliente->getFillable());
    }

    // ==================== TESTES DO REPOSITORY ====================

    /**
     * Teste se o repository pode ser instanciado
     */
    public function test_cliente_repository_pode_ser_instanciado(): void
    {
        $repository = new ClienteRepository();
        $this->assertInstanceOf(ClienteRepository::class, $repository);
    }

    /**
     * Teste se o repository tem o model correto configurado
     */
    public function test_cliente_repository_tem_model_correto(): void
    {
        $model = ClienteRepository::model();
        $this->assertInstanceOf(Cliente::class, $model);
    }

    // ==================== TESTES DO SERVICE ====================

    /**
     * Teste se o service pode ser instanciado
     */
    public function test_cliente_service_pode_ser_instanciado(): void
    {
        $repository = $this->createMock(ClienteRepository::class);
        $service = new ClienteService($repository);
        $this->assertInstanceOf(ClienteService::class, $service);
    }
}

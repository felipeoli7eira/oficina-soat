<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Material;

use App\Domain\Entity\Material\Entidade;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    public function testCriarEntidadeComDadosValidos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético para motores',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Óleo de Motor', $entidade->nome);
        $this->assertEquals('7891234567890', $entidade->gtin);
        $this->assertEquals(100, $entidade->estoque);
        $this->assertEquals(5000, $entidade->preco_custo);
        $this->assertEquals(8000, $entidade->preco_venda);
        $this->assertEquals(6000, $entidade->preco_uso_interno);
        $this->assertEquals('OLE-001', $entidade->sku);
        $this->assertEquals('Óleo sintético para motores', $entidade->descricao);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testCriarEntidadeSemSkuEDescricao()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertNull($entidade->sku);
        $this->assertNull($entidade->descricao);
    }

    public function testValidarNomeComMenosDe3Caracteres()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Ol',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarNomeVazio()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        new Entidade(
            uuid: 'uuid-123',
            nome: '  ',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarEstoqueNegativo()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Estoque deve ser maior ou igual a zero');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: -1,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarEstoqueZero()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 0,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals(0, $entidade->estoque);
    }

    public function testValidarPrecoCustoNegativo()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('preço de custo deve ser maior ou igual a zero');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: -1,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarPrecoVendaNegativo()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('preço de venda deve ser maior ou igual a zero');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: -1,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarPrecoUsoInternoNegativo()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('preço de uso interno deve ser maior ou igual a zero');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: -1,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarGtinVazio()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GTIN não pode ser vazio');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarSkuVazio()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quando informado, SKU não pode ser vazio');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: '',
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testExcluir()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertNull($entidade->deletadoEm);
        $this->assertFalse($entidade->estaExcluido());

        $entidade->excluir();

        $this->assertNotNull($entidade->deletadoEm);
        $this->assertTrue($entidade->estaExcluido());
    }

    public function testEstaExcluido()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
            deletadoEm: new DateTimeImmutable()
        );

        $this->assertTrue($entidade->estaExcluido());
    }

    public function testToHttpResponse()
    {
        $criadoEm = new DateTimeImmutable('2025-01-01 10:00:00');
        $atualizadoEm = new DateTimeImmutable('2025-01-02 15:30:00');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: $criadoEm,
            atualizadoEm: $atualizadoEm
        );

        $response = $entidade->toHttpResponse();

        $this->assertIsArray($response);
        $this->assertEquals('uuid-123', $response['uuid']);
        $this->assertEquals('Óleo de Motor', $response['nome']);
        $this->assertEquals('7891234567890', $response['gtin']);
        $this->assertEquals(100, $response['estoque']);
        $this->assertEquals('OLE-001', $response['sku']);
        $this->assertEquals('Óleo sintético', $response['descricao']);
        $this->assertEquals(50.0, $response['preco_custo']);
        $this->assertEquals(80.0, $response['preco_venda']);
        $this->assertEquals(60.0, $response['preco_uso_interno']);
        $this->assertEquals('01/01/2025 10:00', $response['criado_em']);
        $this->assertEquals('02/01/2025 15:30', $response['atualizado_em']);
    }

    public function testToCreateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $dataArray = $entidade->toCreateDataArray();

        $this->assertIsArray($dataArray);
        $this->assertEquals('Óleo de Motor', $dataArray['nome']);
        $this->assertEquals('7891234567890', $dataArray['gtin']);
        $this->assertEquals(100, $dataArray['estoque']);
        $this->assertEquals('OLE-001', $dataArray['sku']);
        $this->assertEquals('Óleo sintético', $dataArray['descricao']);
        $this->assertEquals(5000, $dataArray['preco_custo']);
        $this->assertEquals(8000, $dataArray['preco_venda']);
        $this->assertEquals(6000, $dataArray['preco_uso_interno']);
    }

    public function testAtualizar()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'nome' => 'Óleo de Motor Premium',
            'estoque' => 150,
            'preco_venda' => 9000
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals('Óleo de Motor Premium', $entidade->nome);
        $this->assertEquals(150, $entidade->estoque);
        $this->assertEquals(9000, $entidade->preco_venda);
    }

    public function testAtualizarComTodosOsCampos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'nome' => 'Óleo de Motor Premium',
            'gtin' => '7891234567891',
            'estoque' => 150,
            'sku' => 'OLE-002',
            'descricao' => 'Óleo sintético premium',
            'preco_custo' => 6000,
            'preco_venda' => 9000,
            'preco_uso_interno' => 7000
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals('Óleo de Motor Premium', $entidade->nome);
        $this->assertEquals('7891234567891', $entidade->gtin);
        $this->assertEquals(150, $entidade->estoque);
        $this->assertEquals('OLE-002', $entidade->sku);
        $this->assertEquals('Óleo sintético premium', $entidade->descricao);
        $this->assertEquals(6000, $entidade->preco_custo);
        $this->assertEquals(9000, $entidade->preco_venda);
        $this->assertEquals(7000, $entidade->preco_uso_interno);
    }

    public function testAtualizarComNomeInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->atualizar(['nome' => 'Ol']);
    }

    public function testAtualizarComEstoqueNegativo()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Estoque deve ser maior ou igual a zero');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: null,
            descricao: null,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->atualizar(['estoque' => -10]);
    }

    public function testToUpdateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Óleo de Motor',
            gtin: '7891234567890',
            estoque: 100,
            preco_custo: 5000,
            preco_venda: 8000,
            preco_uso_interno: 6000,
            sku: 'OLE-001',
            descricao: 'Óleo sintético',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $updateArray = $entidade->toUpdateDataArray();

        $this->assertIsArray($updateArray);
        $this->assertEquals('Óleo de Motor', $updateArray['nome']);
        $this->assertEquals('7891234567890', $updateArray['gtin']);
        $this->assertEquals(100, $updateArray['estoque']);
        $this->assertEquals('OLE-001', $updateArray['sku']);
        $this->assertEquals('Óleo sintético', $updateArray['descricao']);
        $this->assertEquals(5000, $updateArray['preco_custo']);
        $this->assertEquals(8000, $updateArray['preco_venda']);
        $this->assertEquals(6000, $updateArray['preco_uso_interno']);
    }
}

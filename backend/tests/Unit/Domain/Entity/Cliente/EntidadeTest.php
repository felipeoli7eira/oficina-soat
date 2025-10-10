<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    public function testCriarEntidadeComDadosValidos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('João Silva', $entidade->nome);
        $this->assertEquals('12345678901', $entidade->documento);
        $this->assertEquals('joao@example.com', $entidade->email);
        $this->assertEquals('11999999999', $entidade->fone);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testValidarNomeComMenosDe3Caracteres()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Jo',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
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
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testExcluir()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
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
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
            deletadoEm: new DateTimeImmutable()
        );

        $this->assertTrue($entidade->estaExcluido());
    }

    public function testValidarDocumentoCPFValido()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->validarDocumento();
        $this->assertEquals('12345678901', $entidade->documento);
    }

    public function testValidarDocumentoCNPJValido()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Empresa LTDA',
            documento: '12345678901234',
            email: 'empresa@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->validarDocumento();
        $this->assertEquals('12345678901234', $entidade->documento);
    }

    public function testValidarDocumentoInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Documento inválido');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '123456789',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->validarDocumento();
    }

    public function testCpfValidoComCpfVazio()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('CPF não pode ser vazio');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->cpfValido('');
    }

    public function testCpfValidoComTamanhoIncorreto()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('CPF deve ter 11 dígitos');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->cpfValido('123456789');
    }

    public function testCnpjValidoComCnpjVazio()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('CNPJ não pode ser vazio');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Empresa LTDA',
            documento: '12345678901234',
            email: 'empresa@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->cnpjValido('');
    }

    public function testCnpjValidoComTamanhoIncorreto()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('CNPJ deve ter 14 dígitos');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Empresa LTDA',
            documento: '12345678901234',
            email: 'empresa@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->cnpjValido('12345678901');
    }

    public function testValidarEmailValido()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->validarEmail();
        $this->assertEquals('joao@example.com', $entidade->email);
    }

    public function testValidarEmailInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email inválido');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->email = 'email-invalido';
        $entidade->validarEmail();
    }

    public function testDocumentoLimpo()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '123.456.789-01',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('12345678901', $entidade->documentoLimpo());
    }

    public function testDocumentoLimpoCNPJ()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Empresa LTDA',
            documento: '12.345.678/9012-34',
            email: 'empresa@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('12345678901234', $entidade->documentoLimpo());
    }

    public function testToHttpResponse()
    {
        $criadoEm = new DateTimeImmutable('2025-01-01 10:00:00');
        $atualizadoEm = new DateTimeImmutable('2025-01-02 15:30:00');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: $criadoEm,
            atualizadoEm: $atualizadoEm
        );

        $response = $entidade->toHttpResponse();

        $this->assertIsArray($response);
        $this->assertEquals('uuid-123', $response['uuid']);
        $this->assertEquals('João Silva', $response['nome']);
        $this->assertEquals('12345678901', $response['documento']);
        $this->assertEquals('joao@example.com', $response['email']);
        $this->assertEquals('11999999999', $response['fone']);
        $this->assertEquals('01/01/2025 10:00', $response['criado_em']);
        $this->assertEquals('02/01/2025 15:30', $response['atualizado_em']);
    }

    public function testToCreateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '123.456.789-01',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $dataArray = $entidade->toCreateDataArray();

        $this->assertIsArray($dataArray);
        $this->assertEquals('João Silva', $dataArray['nome']);
        $this->assertEquals('12345678901', $dataArray['documento']);
        $this->assertEquals('joao@example.com', $dataArray['email']);
        $this->assertEquals('11999999999', $dataArray['fone']);
    }

    public function testAtualizar()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'nome' => 'João da Silva Santos',
            'email' => 'joao.santos@example.com',
            'fone' => '11988888888'
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals('João da Silva Santos', $entidade->nome);
        $this->assertEquals('joao.santos@example.com', $entidade->email);
        $this->assertEquals('11988888888', $entidade->fone);
    }

    public function testAtualizarComDocumento()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'documento' => '98765432109'
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals('98765432109', $entidade->documento);
    }

    public function testAtualizarComNomeInvalido()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->atualizar(['nome' => 'Jo']);
    }

    public function testToUpdateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'João Silva',
            documento: '12345678901',
            email: 'joao@example.com',
            fone: '11999999999',
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $updateArray = $entidade->toUpdateDataArray();

        $this->assertIsArray($updateArray);
        $this->assertEquals('João Silva', $updateArray['nome']);
        $this->assertEquals('12345678901', $updateArray['documento']);
        $this->assertEquals('joao@example.com', $updateArray['email']);
        $this->assertEquals('11999999999', $updateArray['fone']);
    }
}

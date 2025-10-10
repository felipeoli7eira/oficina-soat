<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Servico;

use App\Domain\Entity\Servico\Entidade;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    public function testCriarEntidadeComDadosValidos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Troca de óleo', $entidade->nome);
        $this->assertEquals(15000, $entidade->valor);
    }

    public function testValidarNomeComMenosDe3Caracteres()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'AB',
            valor: 10000,
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
            nome: '   ',
            valor: 10000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarValorNegativo()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Valor deve ser maior ou igual a zero');

        new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: -100,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarValorZero()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Serviço gratuito',
            valor: 0,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals(0, $entidade->valor);
    }

    public function testExcluirServico()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->excluir();

        $this->assertNotNull($entidade->deletadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
    }

    public function testEstaExcluidoRetornaTrue()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->excluir();

        $this->assertTrue($entidade->estaExcluido());
    }

    public function testEstaExcluidoRetornaFalse()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertFalse($entidade->estaExcluido());
    }

    public function testToHttpResponse()
    {
        $criadoEm = new DateTimeImmutable('2025-01-01 10:00:00');
        $atualizadoEm = new DateTimeImmutable('2025-01-02 15:30:00');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: $criadoEm,
            atualizadoEm: $atualizadoEm
        );

        $response = $entidade->toHttpResponse();

        $this->assertIsArray($response);
        $this->assertEquals('uuid-123', $response['uuid']);
        $this->assertEquals('Troca de óleo', $response['nome']);
        $this->assertEquals(150.0, $response['valor']);
        $this->assertEquals('01/01/2025 10:00', $response['criado_em']);
        $this->assertEquals('02/01/2025 15:30', $response['atualizado_em']);
    }

    public function testToCreateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Troca de óleo',
            valor: 15000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $data = $entidade->toCreateDataArray();

        $this->assertIsArray($data);
        $this->assertEquals('Troca de óleo', $data['nome']);
        $this->assertEquals(15000, $data['valor']);
        $this->assertArrayNotHasKey('uuid', $data);
        $this->assertArrayNotHasKey('criado_em', $data);
        $this->assertArrayNotHasKey('atualizado_em', $data);
    }

    public function testValorEmCentavos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Alinhamento',
            valor: 12050,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $response = $entidade->toHttpResponse();

        $this->assertEquals(120.50, $response['valor']);
    }
}

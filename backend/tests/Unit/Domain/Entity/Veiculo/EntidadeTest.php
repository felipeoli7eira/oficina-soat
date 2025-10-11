<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Veiculo;

use App\Domain\Entity\Veiculo\Entidade;
use App\Exception\DomainHttpException;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    public function testCriarEntidadeComDadosValidos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Toyota', $entidade->marca);
        $this->assertEquals('Corolla', $entidade->modelo);
        $this->assertEquals('ABC1234', $entidade->placa);
        $this->assertEquals(2023, $entidade->ano);
        $this->assertEquals(1, $entidade->clienteId);
        $this->assertNull($entidade->deletadoEm);
    }

    public function testValidarAnoMaiorQueAnoAtual()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ano não pode ser maior que o ano atual');

        new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: (int) date('Y') + 1,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );
    }

    public function testValidarAnoIgualAoAnoAtual()
    {
        $anoAtual = (int) date('Y');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: $anoAtual,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals($anoAtual, $entidade->ano);
    }

    public function testValidarAnoMenorQueAnoAtual()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2020,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $this->assertEquals(2020, $entidade->ano);
    }

    public function testExcluir()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
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
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
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
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: $criadoEm,
            atualizadoEm: $atualizadoEm
        );

        $response = $entidade->toHttpResponse();

        $this->assertIsArray($response);
        $this->assertEquals('uuid-123', $response['uuid']);
        $this->assertEquals('Toyota', $response['marca']);
        $this->assertEquals('Corolla', $response['modelo']);
        $this->assertEquals('ABC1234', $response['placa']);
        $this->assertEquals(2023, $response['ano']);
        $this->assertEquals('01/01/2025 10:00', $response['criado_em']);
        $this->assertEquals('02/01/2025 15:30', $response['atualizado_em']);
    }

    public function testToCreateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $dataArray = $entidade->toCreateDataArray();

        $this->assertIsArray($dataArray);
        $this->assertEquals('Toyota', $dataArray['marca']);
        $this->assertEquals('Corolla', $dataArray['modelo']);
        $this->assertEquals('ABC1234', $dataArray['placa']);
        $this->assertEquals(2023, $dataArray['ano']);
        $this->assertEquals(1, $dataArray['cliente_id']);
    }

    public function testAtualizar()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'placa' => 'XYZ5678'
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals('Honda', $entidade->marca);
        $this->assertEquals('Civic', $entidade->modelo);
        $this->assertEquals('XYZ5678', $entidade->placa);
        $this->assertEquals(2023, $entidade->ano);
    }

    public function testAtualizarComAno()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'ano' => 2022
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals(2022, $entidade->ano);
    }

    public function testAtualizarComAnoInvalido()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Ano não pode ser maior que o ano atual');

        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $entidade->atualizar(['ano' => (int) date('Y') + 1]);
    }

    public function testToUpdateDataArray()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $updateArray = $entidade->toUpdateDataArray();

        $this->assertIsArray($updateArray);
        $this->assertEquals('Toyota', $updateArray['marca']);
        $this->assertEquals('Corolla', $updateArray['modelo']);
        $this->assertEquals('ABC1234', $updateArray['placa']);
        $this->assertEquals(2023, $updateArray['ano']);
    }

    public function testAtualizarTodosOsCampos()
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            marca: 'Toyota',
            modelo: 'Corolla',
            placa: 'ABC1234',
            ano: 2023,
            clienteId: 1,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable()
        );

        $novosDados = [
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'placa' => 'XYZ5678',
            'ano' => 2024
        ];

        $entidade->atualizar($novosDados);

        $this->assertEquals('Honda', $entidade->marca);
        $this->assertEquals('Civic', $entidade->modelo);
        $this->assertEquals('XYZ5678', $entidade->placa);
        $this->assertEquals(2024, $entidade->ano);
    }
}

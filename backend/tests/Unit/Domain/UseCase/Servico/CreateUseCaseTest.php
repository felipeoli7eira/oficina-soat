<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\UseCase\Servico\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    public function testExecComSucesso()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('Troca de óleo', 'nome')
            ->willReturn(null);

        $gateway->expects($this->once())
            ->method('criar')
            ->willReturn([
                'uuid' => 'uuid-gerado-123',
                'nome' => 'Troca de óleo',
                'valor' => 15000,
            ]);

        $useCase = new CreateUseCase(
            nome: 'Troca de óleo',
            valor: 15000
        );

        $resultado = $useCase->exec($gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('uuid-gerado-123', $resultado->uuid);
        $this->assertEquals('Troca de óleo', $resultado->nome);
        $this->assertEquals(15000, $resultado->valor);
    }

    public function testExecComServicoJaCadastrado()
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Serviço já cadastrado');
        $this->expectExceptionCode(400);

        $entidadeExistente = $this->createMock(Entidade::class);

        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('Troca de óleo', 'nome')
            ->willReturn($entidadeExistente);

        $gateway->expects($this->never())
            ->method('criar');

        $useCase = new CreateUseCase(
            nome: 'Troca de óleo',
            valor: 15000
        );

        $useCase->exec($gateway);
    }

    public function testExecComValorZero()
    {
        $gateway = $this->createMock(ServicoGateway::class);

        $gateway->method('encontrarPorIdentificadorUnico')
            ->willReturn(null);

        $gateway->method('criar')
            ->willReturn(['uuid' => 'uuid-123']);

        $useCase = new CreateUseCase(
            nome: 'Serviço gratuito',
            valor: 0
        );

        $resultado = $useCase->exec($gateway);

        $this->assertEquals(0, $resultado->valor);
    }
}

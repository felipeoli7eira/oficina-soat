<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Cliente;

use App\Domain\UseCase\Cliente\ValidateStatusUseCase;
use App\Infrastructure\Gateway\ClienteGateway;
use PHPUnit\Framework\TestCase;

class ValidateStatusUseCaseTest extends TestCase
{
    public function testExecRetornaTrueQuandoClienteAtivo()
    {
        $gateway = $this->createMock(ClienteGateway::class);
        $gateway->expects($this->once())
            ->method('validaStatus')
            ->with('12345678901')
            ->willReturn(true);

        $useCase = new ValidateStatusUseCase(documento: '12345678901');
        $resultado = $useCase->exec($gateway);

        $this->assertTrue($resultado);
    }

    public function testExecRetornaFalseQuandoClienteInativo()
    {
        $gateway = $this->createMock(ClienteGateway::class);
        $gateway->expects($this->once())
            ->method('validaStatus')
            ->with('12345678901')
            ->willReturn(false);

        $useCase = new ValidateStatusUseCase(documento: '12345678901');
        $resultado = $useCase->exec($gateway);

        $this->assertFalse($resultado);
    }

    public function testExecRetornaFalseQuandoDocumentoVazio()
    {
        $gateway = $this->createMock(ClienteGateway::class);
        $gateway->expects($this->never())
            ->method('validaStatus');

        $useCase = new ValidateStatusUseCase(documento: '');
        $resultado = $useCase->exec($gateway);

        $this->assertFalse($resultado);
    }
}

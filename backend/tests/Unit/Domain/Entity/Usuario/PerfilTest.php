<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Usuario;

use App\Domain\Entity\Usuario\Perfil;
use PHPUnit\Framework\TestCase;

class PerfilTest extends TestCase
{
    public function testCasesAsArrayRetornaTodosOsPerfis()
    {
        $perfis = Perfil::casesAsArray();

        $this->assertIsArray($perfis);
        $this->assertCount(4, $perfis);
        $this->assertContains('atendente', $perfis);
        $this->assertContains('comercial', $perfis);
        $this->assertContains('mecanico', $perfis);
        $this->assertContains('gestor_estoque', $perfis);
    }

    public function testPerfilAtendenteValue()
    {
        $this->assertEquals('atendente', Perfil::ATENDENTE->value);
    }

    public function testPerfilComercialValue()
    {
        $this->assertEquals('comercial', Perfil::COMERCIAL->value);
    }

    public function testPerfilMecanicoValue()
    {
        $this->assertEquals('mecanico', Perfil::MECANICO->value);
    }

    public function testPerfilGestorEstoqueValue()
    {
        $this->assertEquals('gestor_estoque', Perfil::GESTOR_ESTOQUE->value);
    }

    public function testCasesRetornaTodosOsCases()
    {
        $cases = Perfil::cases();

        $this->assertCount(4, $cases);
        $this->assertContainsOnlyInstancesOf(Perfil::class, $cases);
    }
}

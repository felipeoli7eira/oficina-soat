<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\UsuarioModel;
use PHPUnit\Framework\TestCase;

class UsuarioModelTest extends TestCase
{
    public function testTable()
    {
        $model = new UsuarioModel();
        $this->assertEquals('usuarios', $model->getTable());
    }

    public function testFillable()
    {
        $model = new UsuarioModel();
        $this->assertIsArray($model->getFillable());
        $this->assertContains('uuid', $model->getFillable());
        $this->assertContains('nome', $model->getFillable());
        $this->assertContains('email', $model->getFillable());
        $this->assertContains('senha', $model->getFillable());
        $this->assertContains('ativo', $model->getFillable());
        $this->assertContains('perfil', $model->getFillable());
    }

    public function testTimestamps()
    {
        $model = new UsuarioModel();
        $this->assertFalse($model->timestamps);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\VeiculoModel;
use PHPUnit\Framework\TestCase;

class VeiculoModelTest extends TestCase
{
    public function testTable()
    {
        $model = new VeiculoModel();
        $this->assertEquals('veiculos', $model->getTable());
    }

    public function testFillable()
    {
        $model = new VeiculoModel();
        $this->assertIsArray($model->getFillable());
        $this->assertContains('uuid', $model->getFillable());
        $this->assertContains('marca', $model->getFillable());
        $this->assertContains('modelo', $model->getFillable());
        $this->assertContains('placa', $model->getFillable());
        $this->assertContains('ano', $model->getFillable());
        $this->assertContains('cliente_id', $model->getFillable());
    }

    public function testTimestamps()
    {
        $model = new VeiculoModel();
        $this->assertFalse($model->timestamps);
    }
}

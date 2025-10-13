<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MaterialModel;
use PHPUnit\Framework\TestCase;

class MaterialModelTest extends TestCase
{
    public function testTable()
    {
        $model = new MaterialModel();
        $this->assertEquals('materiais', $model->getTable());
    }

    public function testFillable()
    {
        $model = new MaterialModel();
        $this->assertIsArray($model->getFillable());
        $this->assertContains('uuid', $model->getFillable());
        $this->assertContains('nome', $model->getFillable());
        $this->assertContains('gtin', $model->getFillable());
        $this->assertContains('sku', $model->getFillable());
        $this->assertContains('estoque', $model->getFillable());
        $this->assertContains('preco_custo', $model->getFillable());
        $this->assertContains('preco_venda', $model->getFillable());
        $this->assertContains('preco_uso_interno', $model->getFillable());
    }

    public function testTimestamps()
    {
        $model = new MaterialModel();
        $this->assertFalse($model->timestamps);
    }
}

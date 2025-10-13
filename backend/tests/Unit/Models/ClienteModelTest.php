<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\ClienteModel;
use PHPUnit\Framework\TestCase;

class ClienteModelTest extends TestCase
{
    public function testTable()
    {
        $model = new ClienteModel();
        $this->assertEquals('clientes', $model->getTable());
    }

    public function testFillable()
    {
        $model = new ClienteModel();
        $this->assertIsArray($model->getFillable());
        $this->assertContains('uuid', $model->getFillable());
        $this->assertContains('nome', $model->getFillable());
        $this->assertContains('documento', $model->getFillable());
        $this->assertContains('email', $model->getFillable());
        $this->assertContains('fone', $model->getFillable());
    }

    public function testTimestamps()
    {
        $model = new ClienteModel();
        $this->assertFalse($model->timestamps);
    }
}

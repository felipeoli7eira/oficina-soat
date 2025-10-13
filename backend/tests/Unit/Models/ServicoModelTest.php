<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\ServicoModel;
use PHPUnit\Framework\TestCase;

class ServicoModelTest extends TestCase
{
    public function testTable()
    {
        $model = new ServicoModel();
        $this->assertEquals('servicos', $model->getTable());
    }

    public function testFillable()
    {
        $model = new ServicoModel();
        $this->assertIsArray($model->getFillable());
        $this->assertContains('uuid', $model->getFillable());
        $this->assertContains('nome', $model->getFillable());
        $this->assertContains('valor', $model->getFillable());
    }

    public function testTimestamps()
    {
        $model = new ServicoModel();
        $this->assertFalse($model->timestamps);
    }
}

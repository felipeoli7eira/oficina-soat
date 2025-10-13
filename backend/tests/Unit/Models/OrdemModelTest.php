<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\OrdemModel;
use PHPUnit\Framework\TestCase;

class OrdemModelTest extends TestCase
{
    public function testTable()
    {
        $model = new OrdemModel();
        $this->assertEquals('os', $model->getTable());
    }

    public function testFillable()
    {
        $model = new OrdemModel();
        $this->assertIsArray($model->getFillable());
        $this->assertContains('uuid', $model->getFillable());
        $this->assertContains('status', $model->getFillable());
    }

    public function testTimestamps()
    {
        $model = new OrdemModel();
        $this->assertFalse($model->timestamps);
    }
}

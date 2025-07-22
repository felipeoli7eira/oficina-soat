<?php

declare(strict_types=1);

namespace App\Modules\Example\Repository;

use App\AbstractRepository;
use App\Modules\Example\Model\Example;
use App\Interfaces\RepositoryInterface;

class ExampleRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = Example::class;

    public function example(array $data): array
    {
        return $data;
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\OS\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\OS\Model\OS;

class Repository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = OS::class;
}

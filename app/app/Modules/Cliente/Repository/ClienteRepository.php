<?php

declare(strict_types=1);

namespace App\Modules\Cliente\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\Cliente\Model\Cliente;

class ClienteRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = Cliente::class;
}

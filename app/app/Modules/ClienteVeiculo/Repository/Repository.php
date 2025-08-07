<?php

declare(strict_types=1);

namespace App\Modules\ClienteVeiculo\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\ClienteVeiculo\Model\ClienteVeiculo;

class Repository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = ClienteVeiculo::class;
}

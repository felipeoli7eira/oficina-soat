<?php

declare(strict_types=1);

namespace App\Modules\Veiculo\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\Veiculo\Model\Veiculo;

class VeiculoRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = Veiculo::class;
}

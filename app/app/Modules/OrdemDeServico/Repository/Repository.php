<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServico\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;

class Repository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = OrdemDeServico::class;
}

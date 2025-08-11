<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoItem\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\OrdemDeServicoItem\Model\OrdemDeServicoItem;

class Repository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = OrdemDeServicoItem::class;
}

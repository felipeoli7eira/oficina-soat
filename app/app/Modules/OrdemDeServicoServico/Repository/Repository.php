<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServicoServico\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\OrdemDeServicoServico\Model\OrdemDeServicoServico;

class Repository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = OrdemDeServicoServico::class;
}

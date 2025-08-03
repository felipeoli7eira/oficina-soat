<?php

declare(strict_types=1);

namespace App\Modules\Servico\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\Servico\Model\Servico;

class ServicoRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = Servico::class;
}

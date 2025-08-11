<?php

declare(strict_types=1);

namespace App\Modules\PecaInsumo\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\PecaInsumo\Model\PecaInsumo;

class PecaInsumoRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = PecaInsumo::class;
}

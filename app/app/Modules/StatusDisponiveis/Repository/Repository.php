<?php

declare(strict_types=1);

namespace App\Modules\StatusDisponiveis\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\StatusDisponiveis\Model\StatusDisponiveis;

class StatusDisponiveisRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = StatusDisponiveis::class;
}

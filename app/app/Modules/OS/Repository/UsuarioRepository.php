<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Repository;

use App\AbstractRepository;
use App\Interfaces\RepositoryInterface;
use App\Modules\Usuario\Model\Usuario;

class UsuarioRepository extends AbstractRepository implements RepositoryInterface
{
    protected static string $model = Usuario::class;
}

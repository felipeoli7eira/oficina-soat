<?php

declare(strict_types=1);

namespace App\Modules\StatusDisponiveis\Service;

use App\Modules\StatusDisponiveis\Dto\CadastroDto;
use App\Modules\StatusDisponiveis\Repository\StatusDisponiveisRepository;

class Service
{
    public function __construct(private readonly StatusDisponiveisRepository $repo) {}

    public function cadastro(CadastroDto $dto)
    {
        return $this->repo->createOrFirst($dto->asArray())->fresh();
    }
}

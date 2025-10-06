<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

class CreateInputModel
{
    public function __construct(
        public string $nome,
        public string $email,
        public string $senha,
        public bool   $ativo,
        public string $perfil,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Interface\Dto\Usuario;

class CriacaoDto
{
    public function __construct(
        public string $nome,
        public string $email,
        public string $senha,
        public string $documento,
    )
    {

    }
}

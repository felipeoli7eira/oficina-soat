<?php

declare(strict_types=1);

namespace App\Infrastructure\Dto;

use DateTimeImmutable;

class UsuarioDto
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $uuid = null,
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $senha = null,
        public readonly ?bool $ativo = null,
        public readonly ?string $perfil = null,
        public readonly ?DateTimeImmutable $criado_em = null,
        public readonly ?DateTimeImmutable $atualizado_em = null,
        public readonly ?DateTimeImmutable $deletado_em = null,
    ) {}
}

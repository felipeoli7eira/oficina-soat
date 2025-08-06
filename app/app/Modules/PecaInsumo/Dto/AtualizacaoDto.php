<?php

declare(strict_types=1);

namespace App\Modules\PecaInsumo\Dto;

class AtualizacaoDto
{
    public function __construct(public readonly array $dados) {}

    public function asArray(): array
    {
        $safe = $this->dados;

        return $safe;
    }

    public function merge(array $dadosAntigos): array
    {
        return array_merge($dadosAntigos, $this->asArray());
    }
}

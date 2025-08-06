<?php

declare(strict_types=1);

namespace App\Modules\OrdemDeServico\Dto;

class AtualizacaoDto
{
    public array $dados = [];

    public function __construct(array $dados = [])
    {
        $this->dados = $dados;
    }

    public function asArray(): array
    {
        $safe = $this->dados;

        if (array_key_exists('uuid', $safe)) {
            unset($safe['uuid']);
        }

        return $safe;
    }

    public function merge(array $dadosAntigos): array
    {
        return array_merge($dadosAntigos, $this->asArray());
    }
}

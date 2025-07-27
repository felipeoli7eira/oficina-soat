<?php


namespace App\Modules\Cliente\Dto;

class AtualizacaoDto
{
    public function __construct(public readonly array $dados) {}

    public function merge(array $dadosAntigos): array
    {
        return array_merge($dadosAntigos, $this->dados);
    }
}

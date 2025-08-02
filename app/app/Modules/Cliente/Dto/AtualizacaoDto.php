<?php

declare(strict_types=1);

namespace App\Modules\Cliente\Dto;

class AtualizacaoDto
{
    public function __construct(public readonly array $dados) {}

    public function asArray(): array
    {
        $safe = $this->dados;

        if (array_key_exists('cpf', $safe)) {
            $safe['cpf'] = str_replace(['.', '-'], '', $safe['cpf']);
        }

        if (array_key_exists('cnpj', $safe)) {
            $safe['cnpj'] = str_replace(['.', '-', '/'], '', $safe['cnpj']);
        }

        if (array_key_exists('telefone_movel', $safe)) {
            $safe['telefone_movel'] = str_replace(['(', ')', '-'], '', $safe['telefone_movel']);
        }

        if (array_key_exists('cep', $safe)) {
            $safe['cep'] = str_replace(['.', '-'], '', $safe['cep']);
        }

        if (array_key_exists('uf', $safe)) {
            $safe['uf'] = strtoupper($safe['uf']);
        }

        return $safe;
    }

    public function merge(array $dadosAntigos): array
    {
        return array_merge($dadosAntigos, $this->asArray());
    }
}

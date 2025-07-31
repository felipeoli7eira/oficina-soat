<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Dto;

class CadastroDto
{
    public function __construct(
        public string $nome,
        public ?string $cpf,
        public ?string $cnpj,
        public string $email,
        public string $telefone_movel,
        public string $cep,
        public string $logradouro,
        public ?string $numero,
        public string $bairro,
        public ?string $complemento,
        public string $cidade,
        public string $uf
    ) {}

    public function asArray(): array
    {
        return [
            'nome'           => $this->nome,
            'cpf'            => str_replace(['.', '-'], '', $this?->cpf ?? ''),
            'cnpj'           => str_replace(['.', '-', '/'], '', $this?->cnpj ?? ''),
            'email'          => $this->email,
            'telefone_movel' => str_replace(['(', ')', '-'], '', $this->telefone_movel),
            'cep'            => str_replace(['.', '-'], '', $this->cep),
            'logradouro'     => $this->logradouro,
            'numero'         => $this->numero,
            'bairro'         => $this->bairro,
            'complemento'    => $this->complemento,
            'cidade'         => $this->cidade,
            'uf'             => strtoupper($this->uf),
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

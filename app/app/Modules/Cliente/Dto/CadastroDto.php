<?php

declare(strict_types=1);

namespace App\Modules\Cliente\Dto;

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
            'cpf'            => $this->cpf,
            'cnpj'           => $this->cnpj,
            'email'          => $this->email,
            'telefone_movel' => $this->telefone_movel,
            'cep'            => $this->cep,
            'logradouro'     => $this->logradouro,
            'numero'         => $this->numero,
            'bairro'         => $this->bairro,
            'complemento'    => $this->complemento,
            'cidade'         => $this->cidade,
            'uf'             => $this->uf,
        ];
    }

    public function filled(): array
    {
        return array_filter($this->asArray(), fn ($value) => !is_null($value));
    }
}

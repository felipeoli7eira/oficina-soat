<?php

declare(strict_types=1);

namespace App\Domain\Entity\Cliente;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Na Clean Architecture, a entidade representa o núcleo do seu domínio - ela deve ser rica em comportamentos e expressar as regras de negócio.
 * Uma entidade pode e deve se auto validar.
 * Podem se compor de outras entidades para realizar uma regra de negócio maior.
 */
class Entidade
{
    public function __construct(
        public string $uuid,
        public string $nome,
        public string $documento,
        public string $email,
        public string $fone,

        public DateTimeImmutable $criadoEm,
        public DateTimeImmutable $atualizadoEm,
        public ?DateTimeImmutable $deletadoEm = null,
    ) {
        $this->validacoes();
    }

    public function validacoes()
    {
        $this->validarNome();

        // ... outros validadores conforme necessidade
    }

    public function excluir(): void
    {
        $this->deletadoEm = new DateTimeImmutable();
        $this->atualizadoEm = new DateTimeImmutable();
    }

    public function estaExcluido(): bool
    {
        return $this->deletadoEm !== null;
    }

    public function validarNome(): void
    {
        if (strlen(trim($this->nome)) < 3) {
            throw new InvalidArgumentException('Nome deve ter pelo menos 3 caracteres');
        }
    }

    public function validarDocumento(): void
    {
        if (strlen($this->documento) === 11) {
            $this->cpfValido($this->documento);
        } else if (strlen($this->documento) === 14) {
            $this->cnpjValido($this->documento);
        } else {
            throw new InvalidArgumentException('Documento inválido');
        }
    }

    public function validarEmail(): void
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email inválido');
        }
    }

    public function validarFone(): void {}

    public function cpfValido(string $cpf): void
    {
        $cpf = trim($cpf);

        if (empty($cpf)) {
            throw new InvalidArgumentException('CPF não pode ser vazio');
        }

        if (strlen($cpf) != 11) {
            throw new InvalidArgumentException('CPF deve ter 11 dígitos');
        }
    }

    public function cnpjValido(string $cnpj): void
    {
        $cnpj = trim($cnpj);

        if (empty($cnpj)) {
            throw new InvalidArgumentException('CNPJ não pode ser vazio');
        }

        if (strlen($cnpj) != 14) {
            throw new InvalidArgumentException('CNPJ deve ter 14 dígitos');
        }
    }

    public function documentoLimpo(): string
    {
        return str_replace(['.', '/', '-'], '', $this->documento);
    }

    public function toHttpResponse(): array
    {
        return [
            'uuid'              => $this->uuid,
            'nome'              => $this->nome,
            'documento'         => $this->documento,
            'email'             => $this->email,
            'fone'              => $this->fone,

            'criado_em'         => $this->criadoEm instanceof DateTimeImmutable ? $this->criadoEm->format('d/m/Y H:i') : null,
            'atualizado_em'     => $this->atualizadoEm instanceof DateTimeImmutable ? $this->atualizadoEm->format('d/m/Y H:i') : null,
        ];
    }

    public function toExternal(): array
    {
        return [
            'uuid'              => $this->uuid,
            'nome'              => $this->nome,
            'documento'         => $this->documento,
            'email'             => $this->email,
            'fone'              => $this->fone,

            'criado_em'         => $this->criadoEm instanceof DateTimeImmutable ? $this->criadoEm->format('d/m/Y H:i') : null,
            'atualizado_em'     => $this->atualizadoEm instanceof DateTimeImmutable ? $this->atualizadoEm->format('d/m/Y H:i') : null,
        ];
    }

    public function toCreateDataArray(): array
    {
        return [
            'nome'      => $this->nome,
            'documento' => $this->documentoLimpo(),
            'email'     => $this->email,
            'fone'      => $this->fone,
        ];
    }

    public function atualizar(array $dados): void
    {
        if (isset($dados['nome'])) {
            $this->nome = $dados['nome'];
        }

        if (isset($dados['documento'])) {
            $this->documento = $dados['documento'];
        }

        if (isset($dados['email'])) {
            $this->email = $dados['email'];
        }

        if (isset($dados['fone'])) {
            $this->fone = $dados['fone'];
        }

        $this->atualizadoEm = new DateTimeImmutable();

        $this->validacoes();
    }

    public function toUpdateDataArray(): array
    {
        return [
            'nome'      => $this->nome,
            'documento' => $this->documento,
            'email'     => $this->email,
            'fone'      => $this->fone,
        ];
    }
}

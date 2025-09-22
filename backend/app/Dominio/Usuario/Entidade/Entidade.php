<?php

declare(strict_types=1);

namespace App\Dominio\Usuario\Entidade;

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
        public string $identificadorUnicoUniversal, // identidade de domínio (uuid)
        public string $nome,
        public string $email,
        public string $senha,
        public string $documento,
        public bool   $ativo,
        public DateTimeImmutable $criadoEm,
        public DateTimeImmutable $atualizadoEm,
        public ?DateTimeImmutable $deletadoEm,
    ) {
        $this->validadores();
    }

    public function validadores()
    {
        $this->validarEmail();
        $this->validarNome();

        // ... outros validadores conforme necessidade
    }

    public function ativar(): void
    {
        $this->ativo = true;
        $this->atualizadoEm = new DateTimeImmutable();
    }

    public function desativar(): void
    {
        $this->ativo = false;
        $this->atualizadoEm = new DateTimeImmutable();
    }

    public function excluir(): void
    {
        $this->deletadoEm = new DateTimeImmutable();
        $this->ativo = false;
        $this->atualizadoEm = new DateTimeImmutable();
    }

    public function estaExcluido(): bool
    {
        return $this->deletadoEm !== null;
    }

    private function validarEmail(): void
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email inválido');
        }
    }

    private function validarNome(): void
    {
        if (strlen(trim($this->nome)) < 3) {
            throw new InvalidArgumentException('Nome deve ter pelo menos 3 caracteres');
        }
    }
}

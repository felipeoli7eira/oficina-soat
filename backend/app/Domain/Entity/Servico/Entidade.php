<?php

declare(strict_types=1);

namespace App\Domain\Entity\Servico;

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
        public string $uuid, // identidade de domínio (uuid)
        public string $nome,
        public int $valor, // preco em centavos
        public DateTimeImmutable $criadoEm,
        public DateTimeImmutable $atualizadoEm,
        public ?DateTimeImmutable $deletadoEm = null,
    ) {
        $this->validadores();
    }

    public function validadores()
    {
        $this->validarNome();
        $this->validarValor();

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

    public function validarValor(): void
    {
        if ($this->valor < 0) {
            throw new InvalidArgumentException('Valor deve ser maior que zero');
        }
    }

    public function toHttpResponse(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'valor'         => $this->valor,
            'criado_em'     => $this->criadoEm,
            'atualizado_em' => $this->atualizadoEm,
            'deletado_em'   => $this->deletadoEm,
        ];
    }

    public function toCreateDataArray(): array
    {
        return [
            'nome'  => $this->nome,
            'valor' => $this->valor,
        ];
    }
}

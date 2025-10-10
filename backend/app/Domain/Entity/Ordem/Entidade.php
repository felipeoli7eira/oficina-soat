<?php

declare(strict_types=1);

namespace App\Domain\Entity\Ordem;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Na Clean Architecture, a entidade representa o núcleo do seu domínio - ela deve ser rica em comportamentos e expressar as regras de negócio.
 * Uma entidade pode e deve se auto validar.
 * Podem se compor de outras entidades para realizar uma regra de negócio maior.
 */
class Entidade
{
    public const STATUS_RECEBIDA                    = 'RECEBIDA';
    public const STATUS_EM_DIAGNOSTICO              = 'EM_DIAGNOSTICO';
    public const STATUS_AGUARDANDO_APROVACAO        = 'AGUARDANDO_APROVACAO';
    public const STATUS_APROVADA                    = 'APROVADA';
    public const STATUS_REPROVADA                   = 'REPROVADA';
    public const STATUS_CANCELADA                   = 'CANCELADA';
    public const STATUS_EM_EXECUSAO                 = 'EM_EXECUSAO';
    public const STATUS_FINALIZADA                  = 'FINALIZADA';
    public const STATUS_ENTREGUE                    = 'ENTREGUE';

    public function __construct(
        public string $uuid,
        public int $clienteId,
        public int $veiculoId,
        public string $descricao,
        public string $status,

        public DateTimeImmutable $criadoEm,
        public DateTimeImmutable $atualizadoEm,
        public ?DateTimeImmutable $deletadoEm = null,
    ) {
        $this->validacoes();
    }

    public function validacoes()
    {
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

    public function atualizar(array $novosDados): void
    {
        $this->atualizadoEm = new DateTimeImmutable();

        $this->validacoes();
    }
}

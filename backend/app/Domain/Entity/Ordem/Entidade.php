<?php

declare(strict_types=1);

namespace App\Domain\Entity\Ordem;

use DateTimeImmutable;

use App\Domain\Entity\Cliente\Entidade as Cliente;
use App\Domain\Entity\Veiculo\Entidade as Veiculo;

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
    public const STATUS_EM_EXECUCAO                 = 'EM_EXECUCAO';
    public const STATUS_FINALIZADA                  = 'FINALIZADA';
    public const STATUS_ENTREGUE                    = 'ENTREGUE';

    public function __construct(
        public string $uuid,
        public readonly Cliente $cliente,
        public readonly Veiculo $veiculo,
        public ?string $descricao = null,
        public ?string $status = self::STATUS_RECEBIDA,
        public DateTimeImmutable $dtAbertura,
        public ?DateTimeImmutable $dtFinalizacao = null,
        public ?DateTimeImmutable $dtAtualizacao = null,
    ) {
        $this->validacoes();
    }

    public function validacoes()
    {
        // ... outros validadores conforme necessidade
    }

    public function encerrar(): void
    {
        $this->status = self::STATUS_FINALIZADA;
        $this->dtAtualizacao = new DateTimeImmutable();
    }

    public function atualizar(array $novosDados): void
    {
        $this->dtAtualizacao = new DateTimeImmutable();

        $this->validacoes();
    }

    public function toExternal(): array
    {
        return [
            'uuid'              => $this->uuid,
            'cliente'           => $this->cliente->toExternal(),
            'veiculo'           => $this->veiculo->toExternal(),
            'descricao'         => $this->descricao,
            'status'            => $this->status,
            'dt_abertura'       => $this->dtAbertura->format('Y-m-d H:i:s'),
            'dt_finalizacao'    => (
                is_null($this->dtFinalizacao)
                ? null
                : $this->dtFinalizacao->format('Y-m-d H:i:s')
            ),
            'dt_atualizacao'    => (
                is_null($this->dtAtualizacao)
                ? null
                : $this->dtAtualizacao->format('Y-m-d H:i:s')
            ),
        ];
    }
}

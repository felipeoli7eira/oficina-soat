<?php

declare(strict_types=1);

namespace App\Domain\Cliente;

use DateTime;

final class Entity
{
    public const STATUS_ATIVO = true;
    public const STATUS_INATIVO = false;

    public function __construct(
        public string $uuid,
        public string $nome,
        public string $documento,
        public string $email,
        public string $fone,

        public DateTime $criadoEm,
        public ?DateTime $atualizadoEm = null,
        public ?DateTime $deletadoEm = null,
    ) {}

    public function asArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'fone'          => $this->fone,
            'email'         => $this->email,
            'documento'     => $this->documento,

            'criado_em'     => $this->criadoEm->format('Y-m-d H:i:s'),
            'deletado_em'   => $this->deletadoEm ? $this->deletadoEm->format('Y-m-d H:i:s') : null,
            'atualizado_em' => $this->atualizadoEm ? $this->atualizadoEm->format('Y-m-d H:i:s') : null,
        ];
    }

    public function toExternal(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'criado_em'     => $this->criadoEm->format('d/m/Y H:i:s'),
        ];
    }

    public function delete(): void
    {
        $this->atualizadoEm = new DateTime();
        $this->deletadoEm   = new DateTime();
    }

    public function update(array $novosDados): void
    {
        if (isset($novosDados['nome'])) {
            $this->nome = $novosDados['nome'];
        }

        if (isset($novosDados['email'])) {
            $this->email = $novosDados['email'];
        }

        if (isset($novosDados['documento'])) {
            $this->documento = password_hash($novosDados['documento'], PASSWORD_BCRYPT);
        }

        if (isset($novosDados['fone'])) {
            $this->fone = $novosDados['fone'];
        }

        $this->atualizadoEm = new DateTime();
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

use DateTime;

final class Entity
{
    public function __construct(
        private string $uuid,
        private string $nome,
        private string $email,
        private string $senhaAcessoSistema,
        private bool $ativo,

        private ProfileEnum $perfil,

        private DateTime $cadastradoEm,
        private ?DateTime $atualizadoEm = null,
        private ?DateTime $deletadoEm = null,
    ) {}

    public function asArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'email'         => $this->email,
            'senha'         => $this->senhaAcessoSistema,
            'ativo'         => $this->ativo,
            'perfil'        => $this->perfil,
            'cadastrado_em' => $this->cadastradoEm->format('Y-m-d H:i:s'),
            'atualizado_em' => $this->atualizadoEm ? $this->atualizadoEm->format('Y-m-d H:i:s') : null,
            'deletado_em'   => $this->deletadoEm ? $this->deletadoEm->format('Y-m-d H:i:s') : null,
        ];
    }

    public function toExternal(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'email'         => $this->email,
            'ativo'         => $this->ativo,
            'criado_em'     => $this->cadastradoEm->format('d/m/Y H:i:s'),
            'atualizado_em' => isset($this->atualizadoEm) ? $this->atualizadoEm->format('d/m/Y H:i:s') : null,
            'deletado_em'   => isset($this->deletadoEm) ? $this->deletadoEm->format('d/m/Y H:i:s') : null,
        ];
    }
}

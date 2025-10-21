<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

use DateTime;

final class Entity
{
    public const STATUS_ATIVO = true;
    public const STATUS_INATIVO = false;

    public function __construct(
        public string $uuid,
        public string $nome,
        public string $email,
        public string $senhaAcessoSistema,
        public bool $ativo,

        public ProfileEnum $perfil,

        public DateTime $cadastradoEm,
        public ?DateTime $atualizadoEm = null,
        public ?DateTime $deletadoEm = null,
    ) {}

    public function asArray(): array
    {
        return [
            'uuid'          => $this->uuid,
            'nome'          => $this->nome,
            'email'         => $this->email,
            'senha'         => $this->senhaAcessoSistema,
            'ativo'         => $this->ativo,
            'perfil'        => $this->perfil->value,
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
            // 'ativo'         => $this->ativo,
            'perfil'        => $this->perfil->value,
            'criado_em'     => $this->cadastradoEm->format('d/m/Y H:i:s'),
            'atualizado_em' => isset($this->atualizadoEm) ? $this->atualizadoEm->format('d/m/Y H:i:s') : null,
            // 'deletado_em'   => isset($this->deletadoEm) ? $this->deletadoEm->format('d/m/Y H:i:s') : null,
        ];
    }

    public function delete(): void
    {
        $this->ativo = false;
        $this->atualizadoEm = new DateTime();
        $this->deletadoEm = new DateTime();
    }

    public function update(array $novosDados): void
    {
        if (isset($novosDados['nome'])) {
            $this->nome = $novosDados['nome'];
        }

        if (isset($novosDados['email'])) {
            $this->email = $novosDados['email'];
        }

        if (isset($novosDados['senha'])) {
            $this->senhaAcessoSistema = password_hash($novosDados['senha'], PASSWORD_BCRYPT);
        }

        if (isset($novosDados['perfil'])) {
            $this->perfil = ProfileEnum::from($novosDados['perfil']);
        }

        if (isset($novosDados['ativo'])) {
            $this->ativo = $novosDados['ativo'];
        }

        $this->atualizadoEm = new DateTime();
    }

    public function passwordVerify(string $plainTextPassword): bool
    {
        return password_verify($plainTextPassword, $this->senhaAcessoSistema);
    }
}

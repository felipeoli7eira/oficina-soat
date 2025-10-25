<?php

declare(strict_types=1);

namespace App\Domain\Cliente;

use App\Exception\DomainHttpException;
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
    ) {
        $this->validarEntrada();
    }

    public function validarEntrada(): void
    {
        $this->garantirNomeValido();
        $this->garantirEmailValido();
        $this->garantirDocumentoValido();
        $this->garantirFoneValido();
    }

    public function garantirFoneValido(): void
    {
        if (empty(trim($this->fone))) {
            throw new DomainHttpException('O telefone é obrigatório', 400);
        }

        $fone = preg_replace('/[^0-9]/', '', $this->fone);

        if (strlen($fone) !== 11) {
            throw new DomainHttpException('O telefone informado é inválido. Deve conter 11 dígitos, incluindo o DDD.', 400);
        }

        if (preg_match('/(\d)\1{10}/', $fone)) {
            throw new DomainHttpException('O telefone informado é inválido.', 400);
        }
    }

    private function garantirNomeValido(): void
    {
        if (empty(trim($this->nome))) {
            throw new DomainHttpException('O nome é obrigatório', 400);
        }

        if (mb_strlen(trim($this->nome)) < 3) {
            throw new DomainHttpException('O nome deve ter no mínimo 3 caracteres', 400);
        }
    }

    public function garantirEmailValido(): void
    {
        if (empty(trim($this->email))) {
            throw new DomainHttpException('O e-mail é obrigatório', 400);
        }

        if (mb_strlen(trim($this->email)) < 3) {
            throw new DomainHttpException('O e-mail deve ter no mínimo 3 caracteres', 400);
        }

        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainHttpException('O e-mail informado é inválido', 400);
        }
    }

    public function garantirDocumentoValido(): void
    {
        $documento = preg_replace('/[^0-9]/', '', $this->documento);

        if (strlen($documento) === 11) {
            if (!$this->validarCpf($documento)) {
                throw new DomainHttpException('O CPF informado é inválido', 400);
            }
            $this->documento = str_replace(['/', '-', '.'], '', $this->documento);
        } elseif (strlen($documento) === 14) {
            if (!$this->validarCnpj($documento)) {
                throw new DomainHttpException('O CNPJ informado é inválido', 400);
            }
            $this->documento = str_replace(['/', '-', '.'], '', $this->documento);
        } else {
            throw new DomainHttpException('O documento informado é inválido', 400);
        }
    }

    public function validarCpf(string $cpf): bool
    {
        if (preg_match('/(\d)\\1{10}/', $cpf)) {
            return false;
        }

        for ($numeroDigitos = 9; $numeroDigitos < 11; $numeroDigitos++) {
            $soma = 0;
            for ($indice = 0; $indice < $numeroDigitos; $indice++) {
                $soma += $cpf[$indice] * (($numeroDigitos + 1) - $indice);
            }
            $digitoVerificadorCalculado = ((10 * $soma) % 11) % 10;
            if ($cpf[$indice] != $digitoVerificadorCalculado) {
                return false;
            }
        }

        return true;
    }

    public function validarCnpj(string $cnpj): bool
    {
        if (preg_match('/(\d)\\1{13}/', $cnpj)) {
            return false;
        }

        for ($numeroDigitos = 12; $numeroDigitos < 14; $numeroDigitos++) {
            $soma = 0;
            $peso = $numeroDigitos - 7;
            for ($indice = 0; $indice < $numeroDigitos; $indice++) {
                $soma += $cnpj[$indice] * $peso;
                $peso = ($peso == 2) ? 9 : $peso - 1;
            }
            $digitoVerificadorCalculado = ((10 * $soma) % 11) % 10;
            if ($cnpj[$indice] != $digitoVerificadorCalculado) {
                return false;
            }
        }

        return true;
    }

    public function documentoFormatado(): string
    {
        $documento = preg_replace('/[^0-9]/', '', $this->documento);

        if (strlen($documento) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $documento);
        }

        if (strlen($documento) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $documento);
        }

        return $this->documento;
    }

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

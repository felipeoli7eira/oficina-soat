<?php

declare(strict_types=1);

namespace App\Domain\Entity\Veiculo;

use App\Exception\DomainHttpException;
use DateTimeImmutable;

/**
 * Na Clean Architecture, a entidade representa o núcleo do seu domínio - ela deve ser rica em comportamentos e expressar as regras de negócio.
 * Uma entidade pode e deve se auto validar.
 * Podem se compor de outras entidades para realizar uma regra de negócio maior.
 */
class Entidade
{
    public function __construct(
        public string $uuid,
        public string $marca,
        public string $modelo,
        public string $placa,
        public int $ano,
        public int $clienteId,

        public DateTimeImmutable $criadoEm,
        public DateTimeImmutable $atualizadoEm,
        public ?DateTimeImmutable $deletadoEm = null,
    ) {
        $this->validacoes();
    }

    public function validacoes()
    {
        $this->validarAno();

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

    /**
     * @throws DomainHttpException
     * @return void
     */
    public function validarAno(): void
    {
        if ($this->ano > (int) date('Y')) {
            throw new DomainHttpException('Ano não pode ser maior que o ano atual');
        }
    }

    public function toHttpResponse(): array
    {
        return [
            'uuid'          => $this->uuid,
            'marca'         => $this->marca,
            'modelo'        => $this->modelo,
            'placa'         => $this->placa,
            'ano'           => $this->ano,
            'criado_em'     => $this->criadoEm instanceof DateTimeImmutable ? $this->criadoEm->format('d/m/Y H:i') : null,
            'atualizado_em' => $this->atualizadoEm instanceof DateTimeImmutable ? $this->atualizadoEm->format('d/m/Y H:i') : null,
        ];
    }

    public function toCreateDataArray(): array
    {
        return [
            'marca'         => $this->marca,
            'modelo'        => $this->modelo,
            'placa'         => $this->placa,
            'ano'           => $this->ano,
            'cliente_id'    => $this->clienteId,
        ];
    }

    public function atualizar(array $dados): void
    {
        if (isset($dados['marca'])) {
            $this->marca = $dados['marca'];
        }

        if (isset($dados['modelo'])) {
            $this->modelo = $dados['modelo'];
        }

        if (isset($dados['placa'])) {
            $this->placa = $dados['placa'];
        }

        if (isset($dados['ano'])) {
            $this->ano = $dados['ano'];
        }

        $this->atualizadoEm = new DateTimeImmutable();

        $this->validacoes();
    }

    public function toUpdateDataArray(): array
    {
        return [
            'marca'  => $this->marca,
            'modelo' => $this->modelo,
            'placa'  => $this->placa,
            'ano'    => $this->ano,
        ];
    }

    public function toExternal(): array
    {
        return [
            'uuid'              => $this->uuid,
            'marca'             => $this->marca,
            'modelo'            => $this->modelo,
            'placa'             => $this->placa,
            'ano'               => $this->ano,
        ];
    }
}

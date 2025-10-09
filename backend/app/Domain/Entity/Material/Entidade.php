<?php

declare(strict_types=1);

namespace App\Domain\Entity\Material;

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
        public string $gtin,
        public int $estoque,
        public int $preco_custo,
        public int $preco_venda,
        public int $preco_uso_interno,
        public ?string $sku,
        public ?string $descricao,

        public DateTimeImmutable $criadoEm,
        public DateTimeImmutable $atualizadoEm,
        public ?DateTimeImmutable $deletadoEm = null,
    ) {
        $this->validacoes();
    }

    public function validacoes()
    {
        $this->validarNome();
        $this->validarValores();
        $this->validarCodigos();

        // ... outros validadores conforme necessidade

        if ($this->estoque < 0) {
            throw new InvalidArgumentException('Estoque deve ser maior ou igual a zero');
        }
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

    public function validarCodigos(): void
    {
        if (is_string($this->sku) && empty($this->sku)) {
            throw new InvalidArgumentException('Quando informado, SKU não pode ser vazio');
        }

        if (empty($this->gtin)) {
            throw new InvalidArgumentException('GTIN não pode ser vazio');
        }
    }

    public function validarNome(): void
    {
        if (strlen(trim($this->nome)) < 3) {
            throw new InvalidArgumentException('Nome deve ter pelo menos 3 caracteres');
        }
    }

    public function validarValores(): void
    {
        if ($this->preco_custo < 0) {
            throw new InvalidArgumentException('preço de custo deve ser maior ou igual a zero');
        }

        if ($this->preco_venda < 0) {
            throw new InvalidArgumentException('preço de venda deve ser maior ou igual a zero');
        }

        if ($this->preco_uso_interno < 0) {
            throw new InvalidArgumentException('preço de uso interno deve ser maior ou igual a zero');
        }
    }

    public function toHttpResponse(): array
    {
        return [
            'uuid'              => $this->uuid,
            'nome'              => $this->nome,
            'gtin'              => $this->gtin,
            'estoque'           => $this->estoque,
            'sku'               => $this->sku,
            'descricao'         => $this->descricao,
            'preco_custo'       => $this->preco_custo / 100,
            'preco_venda'       => $this->preco_venda / 100,
            'preco_uso_interno' => $this->preco_uso_interno / 100,
            'criado_em'         => $this->criadoEm instanceof DateTimeImmutable ? $this->criadoEm->format('d/m/Y H:i') : null,
            'atualizado_em'     => $this->atualizadoEm instanceof DateTimeImmutable ? $this->atualizadoEm->format('d/m/Y H:i') : null,
        ];
    }

    public function toCreateDataArray(): array
    {
        return [
            'nome'              => $this->nome,
            'gtin'              => $this->gtin,
            'estoque'           => $this->estoque,
            'sku'               => $this->sku,
            'descricao'         => $this->descricao,
            'preco_custo'       => $this->preco_custo,
            'preco_venda'       => $this->preco_venda,
            'preco_uso_interno' => $this->preco_uso_interno,
        ];
    }

    public function atualizar(array $dados): void
    {
        if (isset($dados['nome'])) {
            $this->nome = $dados['nome'];
        }

        if (isset($dados['gtin'])) {
            $this->gtin = $dados['gtin'];
        }

        if (isset($dados['estoque'])) {
            $this->estoque = $dados['estoque'];
        }

        if (isset($dados['sku'])) {
            $this->sku = $dados['sku'];
        }

        if (isset($dados['descricao'])) {
            $this->descricao = $dados['descricao'];
        }

        if (isset($dados['preco_custo'])) {
            $this->preco_custo = $dados['preco_custo'];
        }

        if (isset($dados['preco_venda'])) {
            $this->preco_venda = $dados['preco_venda'];
        }

        if (isset($dados['preco_uso_interno'])) {
            $this->preco_uso_interno = $dados['preco_uso_interno'];
        }

        $this->atualizadoEm = new DateTimeImmutable();

        $this->validacoes();
    }

    public function toUpdateDataArray(): array
    {
        return [
            'nome'              => $this->nome,
            'gtin'              => $this->gtin,
            'estoque'           => $this->estoque,
            'sku'               => $this->sku,
            'descricao'         => $this->descricao,
            'preco_custo'       => $this->preco_custo,
            'preco_venda'       => $this->preco_venda,
            'preco_uso_interno' => $this->preco_uso_interno,
        ];
    }
}

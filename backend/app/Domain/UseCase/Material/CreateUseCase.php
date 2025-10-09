<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Infrastructure\Gateway\MaterialGateway;

use DateTimeImmutable;
use App\Exception\DomainHttpException;

class CreateUseCase
{
    public readonly MaterialGateway $gateway;

    public function __construct(
        public string $nome,
        public string $gtin,
        public int $estoque,
        public int $preco_custo,
        public int $preco_venda,
        public int $preco_uso_interno,
        public ?string $sku = null,
        public ?string $descricao = null
    ) {}

    public function exec(MaterialGateway $gateway): Entidade
    {
        // regras de negocio, validacoes...

        $entidade = new Entidade(
            uuid: '',
            nome: $this->nome,
            gtin: $this->gtin,
            sku: $this->sku,
            descricao: $this->descricao,
            estoque: $this->estoque,
            preco_custo: $this->preco_custo,
            preco_venda: $this->preco_venda,
            preco_uso_interno: $this->preco_uso_interno,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        if ($gateway->encontrarPorIdentificadorUnico($this->nome, 'nome') instanceof Entidade) {
            throw new DomainHttpException('Material com nome repetido', 400);
        }

        if ($gateway->encontrarPorIdentificadorUnico($this->gtin, 'gtin') instanceof Entidade) {
            throw new DomainHttpException('GTIN já cadastrado', 400);
        }

        if (is_string($this->sku) && ! empty($this->sku) && $gateway->encontrarPorIdentificadorUnico($this->sku, 'sku') instanceof Entidade) {
            throw new DomainHttpException('SKU já cadastrado', 400);
        }

        $cadastro = $gateway->criar($entidade->toCreateDataArray());

        if (! is_array($cadastro)) {
            throw new DomainHttpException('Erro ao cadastrar', 500);
        }

        $entidade->uuid = $cadastro['uuid'];

        return $entidade;
    }
}

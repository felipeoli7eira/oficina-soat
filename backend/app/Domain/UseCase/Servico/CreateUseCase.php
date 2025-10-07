<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Servico;

use App\Infrastructure\Gateway\ServicoGateway;
use App\Domain\Entity\Servico\Entidade;
use App\Exception\DomainHttpException;
use DateTimeImmutable;

class CreateUseCase
{
    public readonly ServicoGateway $gateway;

    public function __construct(
        public readonly string $nome,
        public readonly int $valor,
    ) {}

    public function exec(ServicoGateway $gateway): Entidade
    {
        // regras de negocio, validacoes...

        $entidade = new Entidade(
            uuid: '',
            nome: $this->nome,
            valor: $this->valor,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        if ($gateway->encontrarPorIdentificadorUnico($this->nome, 'nome') instanceof Entidade) {
            throw new DomainHttpException('Serviço já cadastrado', 400);
        }

        $cadastro = $gateway->criar($entidade->toCreateDataArray());

        if (! is_array($cadastro)) {
            throw new DomainHttpException('Erro ao cadastrar usuário', 500);
        }

        $entidade->uuid = $cadastro['uuid'];

        return $entidade;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Cliente;

use App\Domain\Entity\Cliente\Entidade;
use App\Infrastructure\Gateway\ClienteGateway;

use DateTimeImmutable;
use App\Exception\DomainHttpException;

class CreateUseCase
{
    public readonly ClienteGateway $gateway;

    public function __construct(
        public string $nome,
        public string $documento,
        public string $email,
        public string $fone,
    ) {}

    public function exec(ClienteGateway $gateway): Entidade
    {
        // regras de negocio, validacoes...

        $entidade = new Entidade(
            uuid: '',
            nome: $this->nome,
            documento: $this->documento,
            email: $this->email,
            fone: $this->fone,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        if ($gateway->encontrarPorIdentificadorUnico($entidade->documentoLimpo(), 'documento') instanceof Entidade) {
            throw new DomainHttpException('Cliente com documento repetido', 400);
        }

        if ($gateway->encontrarPorIdentificadorUnico($this->fone, 'fone') instanceof Entidade) {
            throw new DomainHttpException('Fone já cadastrado', 400);
        }


        if ($gateway->encontrarPorIdentificadorUnico($this->email, 'email') instanceof Entidade) {
            throw new DomainHttpException('E-mail já cadastrado', 400);
        }

        $cadastro = $gateway->criar($entidade->toCreateDataArray());

        if (! is_array($cadastro)) {
            throw new DomainHttpException('Erro ao cadastrar', 500);
        }

        return new Entidade(
            uuid: $cadastro['uuid'],
            nome: $cadastro['nome'],
            documento: $cadastro['documento'],
            email: $cadastro['email'],
            fone: $cadastro['fone'],
            criadoEm: new DateTimeImmutable($cadastro['criado_em']),
            atualizadoEm: new DateTimeImmutable($cadastro['atualizado_em']),
        );
    }
}

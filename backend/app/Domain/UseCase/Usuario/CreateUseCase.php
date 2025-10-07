<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

use App\Infrastructure\Gateway\UsuarioGateway;
use App\Infrastructure\Dto\UsuarioDto;
use App\Domain\Entity\Usuario\Entidade;
use App\Exception\DomainHttpException;
use DateTimeImmutable;

class CreateUseCase
{
    public readonly UsuarioGateway $gateway;

    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $senha,
        public readonly string $perfil,
    ) {}

    public function exec(UsuarioGateway $gateway): Entidade
    {
        // regras de negocio, validacoes...

        $entidade = new Entidade(
            uuid: '',
            nome: $this->nome,
            email: $this->email,
            senha: $this->senha,
            perfil: $this->perfil,
            ativo: true,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        if ($gateway->encontrarPorIdentificadorUnico($this->email, 'email') instanceof Entidade) {
            throw new DomainHttpException('E-mail já cadastrado', 400);
        }

        $cadastro = $gateway->criar($entidade->toCreateDataArray());

        if (! is_array($cadastro)) {
            throw new DomainHttpException('Erro ao cadastrar usuário', 500);
        }

        $entidade->uuid = $cadastro['uuid'];

        return $entidade;
    }
}

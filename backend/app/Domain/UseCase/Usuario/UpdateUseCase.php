<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Usuario;

use App\Domain\Entity\Usuario\Entidade;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\UsuarioGateway;
use DateTimeImmutable;

class UpdateUseCase
{
    public function __construct(public readonly UsuarioGateway $gateway) {}

    public function exec(string $uuid, array $novosDados): Entidade
    {
        if (empty($uuid)) {
            throw new DomainHttpException('identificador único não informado', 400);
        }

        if (is_null($this->gateway->encontrarPorIdentificadorUnico($uuid, 'uuid'))) {
            throw new DomainHttpException('Usuário não encontrado', 400);
        }

        $update = $this->gateway->atualizar($uuid, $novosDados);

        if (! is_array($update)) {
            throw new DomainHttpException('Erro ao atualizar usuário', 500);
        }

        return new Entidade(
            uuid: $update['uuid'],
            nome: $update['nome'],
            email: $update['email'],
            senha: $update['senha'],
            ativo: $update['ativo'],
            perfil: $update['perfil'],
            criadoEm: new DateTimeImmutable($update['criado_em']),
            atualizadoEm: new DateTimeImmutable($update['atualizado_em']),
            deletadoEm: $update['deletado_em'] ? new DateTimeImmutable($update['deletado_em']) : null,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Infraestrutura\Repositorio;

use DateTimeImmutable;

use App\Dominio\Usuario\Entidade\Entidade as EntidadeUsuario;
use App\Dominio\Usuario\Repositorio\Contrato as UsuarioRepositorioContrato;
use App\Interface\Dto\JsonPaginado;
use App\Models\UsuarioModel;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuarioRepositorioEloquent implements UsuarioRepositorioContrato
{
    public function obterTodos(int $porPagina = 20, int $pagina = 1): JsonPaginado
    {
        $dados = UsuarioModel::paginate($porPagina, ['*'], 'page', $pagina);

        return new JsonPaginado(
            $dados->items(),
            $dados->total(),
            $dados->perPage(),
            $dados->currentPage(),
            $dados->lastPage()
        );
    }

    public function encontrarPorIdentificadorUnico(string $nomeIdentificador, string $identificador): ?EntidadeUsuario
    {
        $existe = UsuarioModel::where($nomeIdentificador, $identificador)->first();

        if ($existe) {
            return new EntidadeUsuario(
                $existe->uuid,
                $existe->nome,
                $existe->email,
                $existe->senha,
                $existe->documento,
                $existe->ativo,
                new DateTimeImmutable($existe->criado_em),
                new DateTimeImmutable($existe->atualizado_em),
                $existe->deletado_em ? new DateTimeImmutable($existe->deletado_em) : null,
            );
        }

        return null;
    }

    public function criar(EntidadeUsuario $entidade): EntidadeUsuario
    {
        $entidadeEloquent = UsuarioModel::create([
            'uuid'      => Str::uuid()->toString(),
            'nome'      => $entidade->nome,
            'email'     => $entidade->email,
            'senha'     => Hash::make($entidade->senha),
            'documento' => $entidade->documento,
            'ativo'     => true
        ])->refresh();

        return new EntidadeUsuario(
            $entidadeEloquent->uuid,
            $entidadeEloquent->nome,
            $entidadeEloquent->email,
            $entidadeEloquent->senha,
            $entidadeEloquent->documento,
            $entidadeEloquent->ativo,
            new DateTimeImmutable($entidadeEloquent->criado_em),
            new DateTimeImmutable($entidadeEloquent->atualizado_em),
            null,
        );
    }

    public function modificar(int|string $identificador, array $dados, string $nomeIdentificador): EntidadeUsuario
    {
        return new EntidadeUsuario(
            '',
            '',
            '',
            '',
            '',
            false,
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
        );
    }

    public function remover(int|string $identificador, string $nomeIdentificador): bool
    {
        return false;
    }
}

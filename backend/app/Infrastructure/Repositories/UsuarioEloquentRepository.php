<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use DateTimeImmutable;

use App\Domain\Usuario\Entidade;
use App\Domain\Usuario\RepositorioInterface;
use App\Interface\Dto\UsuarioDto;
use App\Models\UsuarioModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsuarioEloquentRepository implements RepositorioInterface
{
    public function __construct(private readonly UsuarioModel $model) {}

    public function encontrarPorIdentificadorUnico(string|int $identificador, ?string $nomeIdentificador = 'uuid'): ?Entidade
    {
        $entidade = $this->model->query()->where($nomeIdentificador, $identificador);

        if ($entidade->exists()) {
            $entidade = $entidade->first();

            return new Entidade(
                $entidade->uuid,
                $entidade->nome,
                $entidade->email,
                $entidade->senha,
                $entidade->ativo,
                new DateTimeImmutable($entidade->criado_em),
                new DateTimeImmutable($entidade->atualizado_em),
                $entidade->deletado_em ? new DateTimeImmutable($entidade->deletado_em) : null,
            );
        }

        return null;
    }

    public function criar(UsuarioDto $dados): Entidade
    {
        $model = $this->model->query()->create([
            'uuid'      => Str::uuid()->toString(),
            'nome'      => $dados->nome,
            'email'     => $dados->email,
            'senha'     => Hash::make($dados->senha),
            'ativo'     => Entidade::STATUS_ATIVO,
        ]);

        $model->refresh();

        return new Entidade(
            $model->uuid,
            $model->nome,
            $model->email,
            $model->senha,
            $model->ativo,
            new DateTimeImmutable($model->criado_em),
            new DateTimeImmutable($model->atualizado_em),
            $model->deletado_em ? new DateTimeImmutable($model->deletado_em) : null,
        );
    }

    // public function obterTodos(int $porPagina = 20, int $pagina = 1): JsonPaginado
    // {
    //     $dados = UsuarioModel::paginate($porPagina, ['*'], 'page', $pagina);

    //     return new JsonPaginado(
    //         $dados->items(),
    //         $dados->total(),
    //         $dados->perPage(),
    //         $dados->currentPage(),
    //         $dados->lastPage()
    //     );
    // }

    // public function modificar(int|string $identificador, array $dados, string $nomeIdentificador): EntidadeUsuario
    // {
    //     return new EntidadeUsuario(
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         false,
    //         new \DateTimeImmutable(),
    //         new \DateTimeImmutable(),
    //         new \DateTimeImmutable(),
    //     );
    // }

    // public function remover(int|string $identificador, string $nomeIdentificador): bool
    // {
    //     return false;
    // }
}

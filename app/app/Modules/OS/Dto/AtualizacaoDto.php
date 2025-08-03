<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Dto;

use Spatie\Permission\Models\Role;

class AtualizacaoDto
{
    public array $dados = [];

    public function __construct(array $dados = [])
    {
        if (array_key_exists('papel', $dados)) {
            $dados['role_id'] = Role::findByName($dados['papel'])->id;

            unset($dados['papel']);
        }

        $this->dados = $dados;
    }

    public function asArray(): array
    {
        $safe = $this->dados;

        if (array_key_exists('nome', $safe)) {
            $safe['nome'] = strtolower(trim($safe['nome']));
        }

        return $safe;
    }

    public function merge(array $dadosAntigos): array
    {
        return array_merge($dadosAntigos, $this->asArray());
    }
}

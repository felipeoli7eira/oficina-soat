<?php

declare(strict_types=1);

namespace App\Modules\Usuario\Dto;

use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AtualizacaoDto
{
    public array $dados = [];

    public function __construct(array $dados = [])
    {
        if (array_key_exists('papel', $dados)) {
            $dados['role'] = Role::findByName($dados['papel']);

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

        if (array_key_exists('senha', $safe)) {
            $safe['senha'] = Hash::make($safe['senha']);
        }

        if (array_key_exists('status', $safe)) {
            $safe['status'] = strtoupper(trim($safe['status']));
        }

        $safe['data_atualizacao'] = now()->format('Y-m-d H:i:s');

        return $safe;
    }

    public function merge(array $dadosAntigos): array
    {
        return array_merge($dadosAntigos, $this->asArray());
    }
}

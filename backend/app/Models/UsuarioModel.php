<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'nome',
        'email',
        'senha',
        'documento',
        'ativo',
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

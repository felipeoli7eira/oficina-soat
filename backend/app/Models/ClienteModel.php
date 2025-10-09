<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'nome',
        'documento',
        'email',
        'fone',

        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

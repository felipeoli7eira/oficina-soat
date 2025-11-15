<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoModel extends Model
{
    protected $table = 'servicos';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'nome',
        'valor',
        'disponivel',
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

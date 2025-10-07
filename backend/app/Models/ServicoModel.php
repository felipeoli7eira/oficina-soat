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
        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

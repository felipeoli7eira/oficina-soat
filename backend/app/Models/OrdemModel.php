<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemModel extends Model
{
    protected $table = 'os';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'cliente_id',
        'veiculo_id',

        'descricao',
        'status',

        'dt_abertura',
        'dt_finalizacao',

        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

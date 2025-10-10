<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeiculoModel extends Model
{
    protected $table = 'veiculos';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'marca',
        'modelo',
        'placa',
        'ano',
        'cliente_id',

        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

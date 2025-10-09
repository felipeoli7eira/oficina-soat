<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialModel extends Model
{
    protected $table = 'materiais';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'nome',
        'gtin',
        'sku',
        'descricao',
        'estoque',
        'preco_custo',
        'preco_venda',
        'preco_uso_interno',

        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];
}

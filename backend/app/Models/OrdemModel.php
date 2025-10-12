<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteModel::class);
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(VeiculoModel::class);
    }
}

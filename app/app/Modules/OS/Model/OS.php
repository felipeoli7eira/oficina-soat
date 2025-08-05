<?php

namespace App\Modules\OS\Model;

use App\Modules\Cliente\Model\Cliente;
use App\Modules\Usuario\Model\Usuario;
use App\Modules\Veiculo\Model\Veiculo;
use App\Traits\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OS extends Model
{
    use SoftDeletes;

    public $table = 'os';

    public $timestamps = false;

    protected $fillable = [
        'data_finalizacao',
        'prazo_validade',
        'cliente_id',
        'veiculo_id',
        'descricao',
        'valor_desconto',
        'valor_total',
        'usuario_id_atendente',
        'usuario_id_mecanico',
        'excluido',
        'data_exclusao',
    ];

    protected $hidden = [
        'id',
        'cliente_id',
        'viculo_id',
    ];

    protected function casts(): array
    {
        return [
            'valor_desconto'   => 'float',
            'valor_total'      => 'float',

            'data_abertura'    => 'datetime:d/m/Y H:i',
            'data_finalizacao' => 'datetime:d/m/Y H:i',
            'data_exclusao'    => 'datetime:d/m/Y H:i',
            'data_atualizacao' => 'datetime:d/m/Y H:i',
        ];
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function veiculo(): HasOne
    {
        return $this->hasOne(Veiculo::class, 'id', 'veiculo_id');
    }

    public function atendente(): HasOne
    {
        return $this->hasOne(Usuario::class, 'id', 'usuario_id_atendente');
    }

    public function mecanico(): HasOne
    {
        return $this->hasOne(Usuario::class, 'id', 'usuario_id_mecanico');
    }
}

<?php

namespace App\Modules\OS\Model;

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

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'data_cadastro'    => 'datetime:d/m/Y H:i',
            'data_exclusao'    => 'datetime:d/m/Y H:i',
            'data_atualizacao' => 'datetime:d/m/Y H:i',
        ];
    }

    // public function role(): HasOne
    // {
    //     return $this->hasOne(Role::class, 'id', 'role_id');
    // }

}

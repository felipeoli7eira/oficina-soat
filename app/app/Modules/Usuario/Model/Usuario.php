<?php

namespace App\Modules\Usuario\Model;

// use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Models\Role;

// use Illuminate\Support\Facades\Schema;

class Usuario extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    // use HasFactory;

    public $table = 'usuario';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'role_id',
        'status',
        'excluido',
        'data_exclusao',
        'data_atualizacao'
]   ;

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'data_cadastro'    => 'datetime:d/m/Y H:i',
            'data_exclusao'    => 'datetime:d/m/Y H:i',
            'data_atualizacao' => 'datetime:d/m/Y H:i',
        ];
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    // protected static function newFactory(): ClienteFactory
    // {
    //     return ClienteFactory::new();
    // }
}

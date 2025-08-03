<?php

namespace App\Modules\Usuario\Model;

use App\Traits\SoftDeletes;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Models\Role;

// use Illuminate\Support\Facades\Schema;

class Usuario extends Model
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasFactory;
    use SoftDeletes;

    public $table = 'usuario';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'role_id',
        'status',
        'excluido',
        'data_exclusao',
        'data_cadastro',
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

    protected static function newFactory(): UsuarioFactory
    {
        return UsuarioFactory::new();
    }
}

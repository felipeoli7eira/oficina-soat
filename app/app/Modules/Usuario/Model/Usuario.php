<?php

namespace App\Modules\Usuario\Model;

use App\Traits\SoftDeletes;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

// use Illuminate\Support\Facades\Schema;

class Usuario extends Model
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasFactory;
    use SoftDeletes;
    use HasRoles;

    public $table = 'usuario';

    public $timestamps = false;

    protected $guard_name = 'web';

    protected $fillable = [
        'nome',
        'status',
        'excluido',
        'data_exclusao',
        'data_cadastro',
        'data_atualizacao'
    ];

    protected $hidden = [
        'id'
    ];

    protected function casts(): array
    {
        return [
            'data_cadastro'    => 'datetime:d/m/Y H:i',
            'data_exclusao'    => 'datetime:d/m/Y H:i',
            'data_atualizacao' => 'datetime:d/m/Y H:i',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected static function newFactory(): UsuarioFactory
    {
        return UsuarioFactory::new();
    }
}

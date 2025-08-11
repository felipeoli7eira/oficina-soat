<?php

namespace App\Modules\Usuario\Model;

use App\Traits\SoftDeletes;
use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UsuarioFactory> */
    use HasFactory;
    use SoftDeletes;
    use HasRoles;

    public $table = 'usuario';

    public $timestamps = false;

    protected $guard_name = 'api';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'status',
        'excluido',
        'data_exclusao',
        'data_cadastro',
        'data_atualizacao'
    ];

    protected $hidden = [
        'id',
        'senha',
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

    /**
     * 🔑 Permite que o JWTAuth use o ID do usuário no token
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->getRoleNames()->first(), // via Spatie
            'uuid' => $this->uuid,
        ];
    }

    // 🔐 Faz com que o Auth use o campo 'senha' no lugar de 'password'
    public function getAuthPassword()
    {
        return $this->senha;
    }
}

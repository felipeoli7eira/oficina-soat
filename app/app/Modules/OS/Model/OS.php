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

    protected $fillable = [];

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

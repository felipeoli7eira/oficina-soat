<?php

namespace App\Modules\Veiculo\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Veiculo extends Model
{
    public $table = 'veiculo';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'marca',
        'modelo',
        'placa',
        'ano_fabricacao',
        'cor',
        'chassi',
        'excluido',
        'data_cadastro',
        'data_atualizacao',
        'data_exclusao'
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}

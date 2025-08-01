<?php

namespace App\Modules\Servico\Model;

use Database\Factories\ServicoFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Servico extends Model
{
    /** @use HasFactory<\Database\Factories\ServicoFactory> */
    use HasFactory;

    public $table = 'servicos';

    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'valor',
        'status',
        'excluido',
        'data_cadastro',
        'data_atualizacao',
]   ;

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    protected static function newFactory(): ServicoFactory
    {
        return ServicoFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                if (empty($model?->uuid)) {
                    $model->uuid = (string) \Illuminate\Support\Str::uuid();
                }
            }
        });
    }
}

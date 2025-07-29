<?php

namespace App\Modules\Cliente\Model;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class Cliente extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    use HasFactory;

    public $table = 'cliente';

    public $timestamps = false;

    protected $fillable = [
        'nome',
        'cpf',
        'cnpj',
        'email',
        'telefone_movel',
        'cep',
        'logradouro',
        'numero',
        'cidade',
        'bairro',
        'uf',
        'complemento',
        'excluido',
        'data_cadastro',
        'data_atualizacao',
]   ;

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    protected static function newFactory(): ClienteFactory
    {
        return ClienteFactory::new();
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

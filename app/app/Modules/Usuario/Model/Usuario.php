<?php

namespace App\Modules\Usuario\Model;

// use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Support\Facades\Schema;

class Usuario extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteFactory> */
    // use HasFactory;

    public $table = 'usuario';

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

    // protected static function newFactory(): ClienteFactory
    // {
    //     return ClienteFactory::new();
    // }
}

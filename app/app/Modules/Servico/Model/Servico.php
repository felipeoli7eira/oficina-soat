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
        'data_exclusao',
        'data_atualizacao',
]   ;

    protected $hidden = [];

    protected function casts(): array
    {
        return [
            'valor' => 'float'
        ];

    }

    protected static function newFactory(): ServicoFactory
    {
        return ServicoFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

    }
}

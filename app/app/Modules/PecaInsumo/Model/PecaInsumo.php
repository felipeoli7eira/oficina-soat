<?php


namespace App\Modules\PecaInsumo\Model;
use Database\Factories\PecaInsumoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PecaInsumo extends Model
{
    /** @use HasFactory<\Database\Factories\PecaInsumoFactory> */
    use HasFactory;

    public $table = 'peca_insumo';

    public $timestamps = false;

    protected $fillable = [
        'gtin',
        'descricao',
        'valor_custo',
        'valor_venda',
        'qtd_atual',
        'qtd_segregada',
        'status',
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
    }

    protected static function newFactory(): PecaInsumoFactory
    {
        return PecaInsumoFactory::new();
    }
}

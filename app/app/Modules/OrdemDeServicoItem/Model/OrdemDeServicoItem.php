<?php

namespace App\Modules\OrdemDeServicoItem\Model;

use App\Modules\PecaInsumo\Model\PecaInsumo;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;

use App\Traits\SoftDeletes;
use Database\Factories\OrdemDeServicoItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrdemDeServicoItem extends Model
{
    use SoftDeletes;

    /** @use HasFactory<\Database\Factories\OrdemDeServicoItemFactory> */
    use HasFactory;

    public $table = 'os_item';

    public $timestamps = false;

    protected $fillable = [
        'peca_insumo_id',
        'os_id',
        'observacao',
        'quantidade',
        'valor',
        'excluido',
        'data_exclusao',
    ];

    protected $hidden = [
        'id',
        'peca_insumo_id',
        'os_id',
    ];

    protected function casts(): array
    {
        return [
            'quantidade'       => 'integer',
            'valor'           => 'decimal:2',
            'excluido'        => 'boolean',
            'data_exclusao'   => 'datetime:d/m/Y H:i',
            'data_cadastro'   => 'datetime:d/m/Y H:i',
            'data_atualizacao' => 'datetime:d/m/Y H:i',
        ];
    }

    public function pecaInsumo(): HasOne
    {
        return $this->hasOne(PecaInsumo::class, 'id', 'peca_insumo_id');
    }

    public function ordemDeServico(): HasOne
    {
        return $this->hasOne(OrdemDeServico::class, 'id', 'os_id');
    }

    protected static function newFactory(): OrdemDeServicoItemFactory
    {
        return OrdemDeServicoItemFactory::new();
    }
}

<?php

namespace App\Modules\OrdemDeServicoServico\Model;

use App\Modules\Servico\Model\Servico;
use App\Modules\OrdemDeServico\Model\OrdemDeServico;

use App\Traits\SoftDeletes;
use Database\Factories\OrdemDeServicoServicoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrdemDeServicoServico extends Model
{
    use SoftDeletes;

    /** @use HasFactory<\Database\Factories\OrdemDeServicoServicoFactory> */
    use HasFactory;

    public $table = 'os_servico';

    public $timestamps = false;

    protected $fillable = [
        'servico_id',
        'os_id',
        'observacao',
        'quantidade',
        'valor',
        'excluido',
        'data_exclusao',
    ];

    protected $hidden = [
        'id',
        'servico_id',
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

    public function servico(): HasOne
    {
        return $this->hasOne(Servico::class, 'id', 'servico_id');
    }

    public function ordemDeServico(): HasOne
    {
        return $this->hasOne(OrdemDeServico::class, 'id', 'os_id');
    }

    protected static function newFactory(): OrdemDeServicoServicoFactory
    {
        return OrdemDeServicoServicoFactory::new();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdemModel extends Model
{
    protected $table = 'os';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'cliente_id',
        'veiculo_id',

        'descricao',
        'status',

        'dt_abertura',
        'dt_finalizacao',

        'criado_em',
        'atualizado_em',
        'deletado_em'
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(ClienteModel::class);
    }

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(VeiculoModel::class);
    }

    public function servicos()
    {
        return $this->belongsToMany(
            ServicoModel::class,   // model relacionado
            'os_servico',          // tabela pivot
            'os_id',               // chave estrangeira da tabela atual (os)
            'servico_id'           // chave estrangeira da tabela relacionada (servicos)
        );
    }

    public function materiais()
    {
        return $this->belongsToMany(
            MaterialModel::class,   // model relacionado
            'os_material',          // tabela pivot
            'os_id',               // chave estrangeira da tabela atual (os)
            'material_id'           // chave estrangeira da tabela relacionada (materiais)
        );
    }
}

<?php


namespace App\Modules\Veiculo\Model;
use Database\Factories\VeiculoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Veiculo extends Model
{
    /** @use HasFactory<\Database\Factories\VeiculoFactory> */
    use HasFactory;

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

    protected static function newFactory(): VeiculoFactory
    {
        return VeiculoFactory::new();
    }
}

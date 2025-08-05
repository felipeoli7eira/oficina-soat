<?php


namespace App\Modules\ClienteVeiculo\Model;
use Database\Factories\ClienteVeiculoVeiculoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClienteVeiculo extends Model
{
    /** @use HasFactory<\Database\Factories\ClienteVeiculoFactory> */
    use HasFactory;

    public $table = 'cliente_veiculo';

    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'veiculo_id'
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

    protected static function newFactory(): ClienteVeiculoFactory
    {
        return ClienteVeiculoFactory::new();
    }
}

<?php

namespace App\Modules\StatusDisponiveis\Model;

use Database\Factories\StatusDisponiveisFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;

class StatusDisponiveis extends Model
{
    /** @use HasFactory<\Database\Factories\StatusDisponiveisFactory> */
    use HasFactory;

    public $table = 'status_disponiveis';

    public $timestamps = false;

    protected $fillable = [
        'descricao',
        'ordem',
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];

    }

    protected static function newFactory(): StatusDisponiveisFactory
    {
        return StatusDisponiveisFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

    }
}

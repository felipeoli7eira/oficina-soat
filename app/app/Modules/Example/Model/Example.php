<?php

namespace App\Modules\Example\Model;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;

class Example extends Model
{
    /** @use HasFactory<\Database\Factories\{Name}Factory> */
    // use HasFactory;

    protected $fillable = [
        'message'
    ];

    protected $hidden = [];

    protected function casts(): array
    {
        return [];
    }

    // protected static function newFactory(): {Name}Factory
    // {
    //     return {Name}Factory::new();
    // }
}

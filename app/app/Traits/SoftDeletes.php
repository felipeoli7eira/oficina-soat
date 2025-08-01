<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SoftDeletes
{
    /** Boot do trait */
    public static function bootSoftDeletes()
    {
        // Global scope pra sempre filtrar os excluídos
        // Isso significa que TODAS as queries do model que estiver usando essa trait vão automaticamente ter WHERE excluido = false
        // Então não precisa fazer esse filtro manualmente
        static::addGlobalScope('nao_excluidos', function (Builder $builder) {
            $builder->where('excluido', false);
        });
    }

    public function delete()
    {
        $this->update([
            'excluido'      => true,
            'data_exclusao' => now()
        ]);

        return true;
    }

    public function restore()
    {
        $this->update([
            'excluido'      => false,
            'data_exclusao' => null
        ]);

        return true;
    }

    /** Força a exclusão real */
    public function forceDelete()
    {
        return parent::delete();
    }

    public function isDeleted()
    {
        return $this->excluido;
    }

    public function scopeComExcluidos($query)
    {
        return $query->withoutGlobalScope('nao_excluidos');
    }

    public function scopeApenasExcluidos($query)
    {
        return $query->withoutGlobalScope('nao_excluidos')->where('excluido', true);
    }
}

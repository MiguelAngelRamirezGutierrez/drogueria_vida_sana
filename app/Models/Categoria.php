<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    /**
     * Relación: Una categoría tiene muchos productos.
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    /**
     * Scope para filtrar únicamente categorías activas.
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Alias para compatibilidad de scope activos().
     */
    public function scopeActivos($query)
    {
        return $this->scopeActivas($query);
    }
}

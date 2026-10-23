<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Producto
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'estado',
        'name',
        'price',
        'active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categoria_id')->withTrashed();
    }

    /**
     * Relación: Un medicamento tiene múltiples lotes sanitarios.
     */
    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'product_id');
    }

    /**
     * Relación: Lotes no vencidos ordenados bajo criterio FEFO.
     */
    public function activeLots(): HasMany
    {
        return $this->hasMany(Lot::class, 'product_id')
            ->whereDate('expiration_date', '>=', now()->toDateString())
            ->orderBy('expiration_date', 'asc');
    }
}

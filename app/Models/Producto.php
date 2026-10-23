<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

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

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'stock' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function getNameAttribute()
    {
        return $this->attributes['nombre'] ?? $this->attributes['name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nombre'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['precio'] ?? $this->attributes['price'] ?? 0;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['precio'] = $value;
    }

    public function getActiveAttribute()
    {
        return (bool) ($this->attributes['estado'] ?? $this->attributes['active'] ?? true);
    }

    public function setActiveAttribute($value)
    {
        $this->attributes['estado'] = (bool) $value;
    }

    /**
     * Relación: Un producto pertenece a una categoría (aunque esté en papelera).
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id')->withTrashed();
    }

    /**
     * Relación: Un producto tiene muchos detalles de pedidos.
     */
    public function pedidoDetalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class, 'producto_id');
    }

    /**
     * Relación: Un producto tiene muchos detalles de ventas (Clase 9).
     */
    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, 'product_id');
    }

    /**
     * Relación: Un medicamento tiene múltiples lotes sanitarios (Segundo Parcial).
     */
    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'product_id');
    }

    /**
     * Relación: Lotes vigentes (no vencidos) ordenados por FEFO.
     */
    public function activeLots(): HasMany
    {
        return $this->hasMany(Lot::class, 'product_id')
            ->whereDate('expiration_date', '>=', now()->toDateString())
            ->orderBy('expiration_date', 'asc');
    }

    /**
     * Scope para productos activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para productos con stock disponible.
     */
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}

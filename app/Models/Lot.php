<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lots';

    protected $fillable = [
        'product_id',
        'lot_number',
        'expiration_date',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    /**
     * Relación: Un lote pertenece a un producto (medicamento).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope para lotes no vencidos (vigentes a la fecha actual).
     */
    public function scopeVigentes($query)
    {
        return $query->whereDate('expiration_date', '>=', now()->toDateString());
    }

    /**
     * Scope para lotes vencidos.
     */
    public function scopeVencidos($query)
    {
        return $query->whereDate('expiration_date', '<', now()->toDateString());
    }

    /**
     * Scope ordenado según criterio sanitario FEFO (First Expired, First Out).
     */
    public function scopeFefo($query)
    {
        return $query->orderBy('expiration_date', 'asc');
    }

    /**
     * Atributo computado para verificar si el lote está vencido.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date ? $this->expiration_date->isPast() : false;
    }
}

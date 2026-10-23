<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domiciliario extends Model
{
    use HasFactory;

    protected $table = 'domiciliarios';

    protected $fillable = [
        'nombre',
        'telefono',
        'documento',
        'vehiculo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    /**
     * Relación: Un domiciliario tiene muchos pedidos asignados.
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'domiciliario_id');
    }

    /**
     * Scope para domiciliarios activos / disponibles.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}

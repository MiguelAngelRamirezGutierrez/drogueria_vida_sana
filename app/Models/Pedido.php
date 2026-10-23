<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'cliente_id',
        'domiciliario_id',
        'metodo_pago_id',
        'direccion_entrega',
        'estado',
        'total',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    /**
     * Estados permitidos en el ciclo de vida del pedido:
     * Cliente -> Pedido -> Preparación -> Pedido listo -> Domicilio -> Entrega
     */
    public const ESTADOS = [
        'Recibido',
        'Preparando',
        'Listo',
        'En camino',
        'Entregado',
        'Cancelado',
    ];

    /**
     * Relación: Un pedido pertenece a un cliente.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relación: Un pedido puede tener un domiciliario asignado.
     */
    public function domiciliario(): BelongsTo
    {
        return $this->belongsTo(Domiciliario::class, 'domiciliario_id');
    }

    /**
     * Relación: Un pedido pertenece a un método de pago.
     */
    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    /**
     * Relación: Un pedido tiene muchos detalles.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }

    /**
     * Alias de relación para compatibilidad semántica.
     */
    public function pedidoDetalles(): HasMany
    {
        return $this->detalles();
    }

    /**
     * Scope para pedidos pendientes en flujo operativo (no terminados ni cancelados).
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['Recibido', 'Preparando', 'Listo', 'En camino']);
    }

    /**
     * Scope para pedidos del día actual.
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope para pedidos entregados con éxito.
     */
    public function scopeEntregados($query)
    {
        return $query->where('estado', 'Entregado');
    }

    /**
     * Scope para pedidos cancelados.
     */
    public function scopeCancelados($query)
    {
        return $query->where('estado', 'Cancelado');
    }
}

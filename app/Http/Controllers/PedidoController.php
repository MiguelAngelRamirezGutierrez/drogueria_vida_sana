<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Domiciliario;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    /**
     * Muestra el listado de pedidos con eager loading para evitar problemas N+1.
     */
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'domiciliario', 'metodoPago', 'detalles.producto'])
            ->latest();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por ID o por nombre de cliente
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('id', $buscar)
                  ->orWhereHas('cliente', function ($cq) use ($buscar) {
                      $cq->where('nombre', 'like', "%{$buscar}%")
                         ->orWhere('telefono', 'like', "%{$buscar}%");
                  });
            });
        }

        $pedidos = $query->paginate(10)->withQueryString();

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Muestra el formulario para crear un nuevo pedido.
     */
    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $domiciliarios = Domiciliario::activos()->orderBy('nombre')->get();
        $metodosPago = MetodoPago::activos()->orderBy('nombre')->get();
        $productos = Producto::with('categoria')
            ->activos()
            ->conStock()
            ->orderBy('nombre')
            ->get();

        return view('pedidos.create', compact('clientes', 'domiciliarios', 'metodosPago', 'productos'));
    }

    /**
     * Almacena un nuevo pedido en la base de datos junto con sus detalles.
     * Descuenta automáticamente el stock de los productos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'domiciliario_id' => 'nullable|exists:domiciliarios,id',
            'direccion_entrega' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ], [
            'cliente_id.required' => 'Debe seleccionar un cliente obligatorio.',
            'metodo_pago_id.required' => 'Debe seleccionar un método de pago obligatorio.',
            'direccion_entrega.required' => 'La dirección de entrega es obligatoria.',
            'items.required' => 'Debe agregar al menos un producto al pedido.',
            'items.min' => 'El pedido debe contener como mínimo un producto.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a cero.',
        ]);

        $pedido = DB::transaction(function () use ($request) {
            $totalPedido = 0;

            // 1. Crear cabecera del pedido con estado inicial "Recibido"
            $pedido = Pedido::create([
                'cliente_id' => $request->cliente_id,
                'domiciliario_id' => $request->domiciliario_id ?: null,
                'metodo_pago_id' => $request->metodo_pago_id,
                'direccion_entrega' => $request->direccion_entrega,
                'estado' => 'Recibido',
                'total' => 0,
                'observaciones' => $request->observaciones,
            ]);

            // 2. Procesar ítems y descontar inventario
            foreach ($request->items as $itemData) {
                $producto = Producto::lockForUpdate()->findOrFail($itemData['producto_id']);
                $cantidad = (int) $itemData['cantidad'];

                // Validación de stock disponible
                if ($producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para el producto '{$producto->nombre}'. Disponible: {$producto->stock}, Solicitado: {$cantidad}");
                }

                $subtotal = $producto->precio * $cantidad;
                $totalPedido += $subtotal;

                PedidoDetalle::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ]);

                // Descuento de inventario sencillo y directo
                $producto->decrement('stock', $cantidad);
            }

            // 3. Actualizar total final calculado
            $pedido->update(['total' => $totalPedido]);

            return $pedido;
        });

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', "¡Pedido #{$pedido->id} creado exitosamente con estado 'Recibido'!");
    }

    /**
     * Muestra la comanda / factura detallada de un pedido específico.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'domiciliario', 'metodoPago', 'detalles.producto.categoria']);
        $domiciliarios = Domiciliario::activos()->get();

        return view('pedidos.show', compact('pedido', 'domiciliarios'));
    }

    /**
     * Muestra el formulario para editar datos generales o estado del pedido.
     */
    public function edit(Pedido $pedido)
    {
        $pedido->load(['cliente', 'domiciliario', 'metodoPago', 'detalles.producto']);
        $clientes = Cliente::activos()->get();
        $domiciliarios = Domiciliario::activos()->get();
        $metodosPago = MetodoPago::activos()->get();

        return view('pedidos.edit', compact('pedido', 'clientes', 'domiciliarios', 'metodosPago'));
    }

    /**
     * Actualiza el pedido (domiciliario, dirección, observaciones o estado).
     */
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'domiciliario_id' => 'nullable|exists:domiciliarios,id',
            'direccion_entrega' => 'required|string|max:255',
            'estado' => 'required|in:' . implode(',', Pedido::ESTADOS),
            'observaciones' => 'nullable|string|max:500',
        ]);

        $estadoAnterior = $pedido->estado;
        $nuevoEstado = $request->estado;

        // Si se cancela un pedido que antes no estaba cancelado, reponer stock
        if ($nuevoEstado === 'Cancelado' && $estadoAnterior !== 'Cancelado') {
            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }

        $pedido->update([
            'domiciliario_id' => $request->domiciliario_id ?: null,
            'direccion_entrega' => $request->direccion_entrega,
            'estado' => $nuevoEstado,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', "Pedido #{$pedido->id} actualizado correctamente.");
    }

    /**
     * Transición rápida de estado en el flujo operativo:
     * Recibido -> Preparando -> Listo -> En camino -> Entregado / Cancelado
     */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:' . implode(',', Pedido::ESTADOS),
            'domiciliario_id' => 'nullable|exists:domiciliarios,id',
        ]);

        $estadoAnterior = $pedido->estado;
        $nuevoEstado = $request->estado;

        // Si se asignó domiciliario en este paso
        if ($request->filled('domiciliario_id')) {
            $pedido->domiciliario_id = $request->domiciliario_id;
        }

        // Si se cancela el pedido, reponer inventario
        if ($nuevoEstado === 'Cancelado' && $estadoAnterior !== 'Cancelado') {
            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }

        $pedido->estado = $nuevoEstado;
        $pedido->save();

        return redirect()->back()
            ->with('success', "El estado del pedido #{$pedido->id} cambió a '{$nuevoEstado}'.");
    }

    /**
     * Elimina un pedido y sus detalles en cascada.
     */
    public function destroy(Pedido $pedido)
    {
        // Si no estaba cancelado ni entregado, reponer stock antes de eliminar
        if (!in_array($pedido->estado, ['Cancelado', 'Entregado'])) {
            foreach ($pedido->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }

        $id = $pedido->id;
        $pedido->delete();

        return redirect()->route('pedidos.index')
            ->with('success', "Pedido #{$id} eliminado satisfactoriamente.");
    }
}

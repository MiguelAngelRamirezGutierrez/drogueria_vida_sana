@extends('layouts.app')

@section('title', 'Comanda de Pedido #' . $pedido->id)
@section('page_title', 'Detalle de Pedido & Comanda #' . $pedido->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Barra de Navegación y Acciones -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('pedidos.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver al Listado
        </a>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Imprimir Comanda</span>
            </button>
            <a href="{{ route('pedidos.edit', $pedido) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg transition flex items-center space-x-1.5">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Editar</span>
            </a>
        </div>
    </div>

    <!-- Panel de Avance Operativo de Estado (Ciclo de Vida) -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-4 no-print">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Control de Flujo de Operación</h3>
                <p class="text-xs text-slate-500">Transición del estado del pedido según el avance en cocina y despacho.</p>
            </div>
            @php
                $badge = match($pedido->estado) {
                    'Recibido' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'Preparando' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'Listo' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'En camino' => 'bg-purple-100 text-purple-800 border-purple-200',
                    'Entregado' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    default => 'bg-rose-100 text-rose-800 border-rose-200',
                };
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badge }}">
                Estado Actual: {{ $pedido->estado }}
            </span>
        </div>

        <!-- Botones de Transición Rápida -->
        <div class="flex flex-wrap items-center gap-2">
            @if($pedido->estado === 'Recibido')
                <form action="{{ route('pedidos.cambiar-estado', $pedido) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="Preparando">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-fire"></i>
                        <span>1. Pasar a "Preparando" en Cocina</span>
                    </button>
                </form>
            @elseif($pedido->estado === 'Preparando')
                <form action="{{ route('pedidos.cambiar-estado', $pedido) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="Listo">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-box"></i>
                        <span>2. Marcar como "Listo" (Empacado)</span>
                    </button>
                </form>
            @elseif($pedido->estado === 'Listo')
                <form action="{{ route('pedidos.cambiar-estado', $pedido) }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="En camino">
                    @if(!$pedido->domiciliario_id)
                        <select name="domiciliario_id" required class="text-xs rounded-lg border border-slate-300 p-2 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                            <option value="">-- Asignar domiciliario --</option>
                            @foreach($domiciliarios as $d)
                                <option value="{{ $d->id }}">{{ $d->nombre }} ({{ $d->vehiculo }})</option>
                            @endforeach
                        </select>
                    @endif
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-motorcycle"></i>
                        <span>3. Despachar a "En camino"</span>
                    </button>
                </form>
            @elseif($pedido->estado === 'En camino')
                <form action="{{ route('pedidos.cambiar-estado', $pedido) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="Entregado">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>4. Confirmar "Entregado" con Éxito</span>
                    </button>
                </form>
            @elseif($pedido->estado === 'Entregado')
                <div class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200 flex items-center space-x-2">
                    <i class="fa-solid fa-check-double text-emerald-600"></i>
                    <span>Este pedido ha sido completado y entregado al cliente.</span>
                </div>
            @endif

            @if(!in_array($pedido->estado, ['Entregado', 'Cancelado']))
                <form action="{{ route('pedidos.cambiar-estado', $pedido) }}" method="POST" onsubmit="return confirm('¿Seguro que desea cancelar este pedido? Se repondrá el inventario.');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="Cancelado">
                    <button type="submit" class="bg-slate-100 hover:bg-rose-50 text-slate-500 hover:text-rose-600 text-xs font-semibold px-3 py-2 rounded-lg transition border border-slate-200">
                        Cancelar Pedido
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Comanda / Factura Formal del Pedido -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 space-y-6">

        <!-- Cabecera de la Comanda -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="text-2xl font-black text-slate-900 tracking-tight">QuickFood</span>
                    <span class="text-xs font-bold px-2 py-0.5 bg-orange-100 text-orange-800 rounded">ERP & Kitchen</span>
                </div>
                <p class="text-xs text-slate-500">Preparación de Alimentos y Domicilios</p>
                <p class="text-xs text-slate-400">NIT: 901.452.368-1 &bull; Tel: (602) 555-FOOD</p>
            </div>
            <div class="text-right space-y-1">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Orden de Pedido</div>
                <div class="text-3xl font-black text-orange-600">#{{ $pedido->id }}</div>
                <div class="text-xs text-slate-500">{{ $pedido->created_at->isoFormat('D [de] MMMM, YYYY - h:mm A') }}</div>
            </div>
        </div>

        <!-- Datos del Cliente & Logística de Despacho -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs border-b border-slate-200 pb-6">
            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h4 class="font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 text-[11px] text-orange-600">
                    <i class="fa-solid fa-user"></i>
                    <span>Datos del Cliente</span>
                </h4>
                <div class="space-y-1 text-slate-600">
                    <p><strong class="text-slate-800">Nombre:</strong> {{ $pedido->cliente->nombre }}</p>
                    <p><strong class="text-slate-800">Teléfono:</strong> {{ $pedido->cliente->telefono }}</p>
                    <p><strong class="text-slate-800">Email:</strong> {{ $pedido->cliente->email ?? 'No registrado' }}</p>
                    <p><strong class="text-slate-800">Dirección de Entrega:</strong> <span class="font-semibold text-slate-900">{{ $pedido->direccion_entrega }}</span></p>
                </div>
            </div>

            <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                <h4 class="font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-1.5 text-[11px] text-orange-600">
                    <i class="fa-solid fa-motorcycle"></i>
                    <span>Logística & Cobro</span>
                </h4>
                <div class="space-y-1 text-slate-600">
                    <p><strong class="text-slate-800">Método de Pago:</strong> <span class="bg-white px-2 py-0.5 rounded border border-slate-200 font-semibold text-slate-900">{{ $pedido->metodoPago->nombre }}</span></p>
                    <p>
                        <strong class="text-slate-800">Domiciliario:</strong>
                        @if($pedido->domiciliario)
                            <span class="font-semibold text-slate-900">{{ $pedido->domiciliario->nombre }}</span> ({{ $pedido->domiciliario->telefono }})
                            <div class="text-[11px] text-slate-400 mt-0.5">Vehículo: {{ $pedido->domiciliario->vehiculo }}</div>
                        @else
                            <span class="text-slate-400 italic">Pendiente por asignar en despacho</span>
                        @endif
                    </p>
                    @if($pedido->observaciones)
                        <div class="mt-2 pt-2 border-t border-slate-200">
                            <strong class="text-slate-800">Notas para cocina:</strong>
                            <p class="text-slate-700 italic">{{ $pedido->observaciones }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabla de Productos / Comanda -->
        <div class="space-y-3">
            <h4 class="font-bold text-slate-800 uppercase tracking-wider text-xs flex items-center space-x-2">
                <i class="fa-solid fa-receipt text-orange-600"></i>
                <span>Desglose de Ítems Solicitados</span>
            </h4>

            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Producto</th>
                        <th class="py-2.5 px-3">Categoría</th>
                        <th class="py-2.5 px-3 text-center">Cant.</th>
                        <th class="py-2.5 px-3 text-right">Precio Unitario</th>
                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($pedido->detalles as $detalle)
                        <tr>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900">{{ $detalle->producto->nombre }}</div>
                                <div class="text-[11px] text-slate-400">{{ $detalle->producto->descripcion }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-500">
                                {{ $detalle->producto->categoria->nombre }}
                            </td>
                            <td class="py-3 px-3 text-center font-black text-slate-800">
                                {{ $detalle->cantidad }}
                            </td>
                            <td class="py-3 px-3 text-right text-slate-600">
                                ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-right font-black text-slate-900">
                                ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-300 font-bold">
                    <tr>
                        <td colspan="4" class="py-3 px-3 text-right uppercase tracking-wider text-slate-600 text-xs">
                            Total General a Pagar:
                        </td>
                        <td class="py-3 px-3 text-right text-base font-black text-orange-600">
                            ${{ number_format($pedido->total, 0, ',', '.') }} COP
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Pie de Ticket / Comanda -->
        <div class="border-t border-slate-200 pt-6 text-center text-xs text-slate-400 space-y-1">
            <p class="font-medium text-slate-600">¡Gracias por preferir a QuickFood ERP!</p>
            <p>Comanda generada automáticamente por el sistema de gestión.</p>
        </div>

    </div>

</div>
@endsection

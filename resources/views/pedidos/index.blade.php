@extends('layouts.app')

@section('title', 'Listado de Pedidos')
@section('page_title', 'Gestión de Pedidos & Domicilios')

@section('content')
<div class="space-y-6">

    <!-- Barra de Filtros y Búsqueda -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Pestañas de Estados Rápidos -->
            <div class="flex flex-wrap gap-1.5 text-xs font-semibold">
                <a href="{{ route('pedidos.index') }}" class="px-3 py-1.5 rounded-lg border transition {{ !request('estado') ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200' }}">
                    Todos ({{ \App\Models\Pedido::count() }})
                </a>
                <a href="{{ route('pedidos.index', ['estado' => 'Recibido']) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('estado') === 'Recibido' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-200' }}">
                    Recibido ({{ \App\Models\Pedido::where('estado', 'Recibido')->count() }})
                </a>
                <a href="{{ route('pedidos.index', ['estado' => 'Preparando']) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('estado') === 'Preparando' ? 'bg-amber-500 text-white border-amber-500' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border-amber-200' }}">
                    Preparando ({{ \App\Models\Pedido::where('estado', 'Preparando')->count() }})
                </a>
                <a href="{{ route('pedidos.index', ['estado' => 'Listo']) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('estado') === 'Listo' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border-indigo-200' }}">
                    Listo ({{ \App\Models\Pedido::where('estado', 'Listo')->count() }})
                </a>
                <a href="{{ route('pedidos.index', ['estado' => 'En camino']) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('estado') === 'En camino' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-700 hover:bg-purple-100 border-purple-200' }}">
                    En camino ({{ \App\Models\Pedido::where('estado', 'En camino')->count() }})
                </a>
                <a href="{{ route('pedidos.index', ['estado' => 'Entregado']) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('estado') === 'Entregado' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200' }}">
                    Entregado ({{ \App\Models\Pedido::where('estado', 'Entregado')->count() }})
                </a>
                <a href="{{ route('pedidos.index', ['estado' => 'Cancelado']) }}" class="px-3 py-1.5 rounded-lg border transition {{ request('estado') === 'Cancelado' ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border-rose-200' }}">
                    Cancelado ({{ \App\Models\Pedido::where('estado', 'Cancelado')->count() }})
                </a>
            </div>

            <!-- Formulario de Búsqueda -->
            <form action="{{ route('pedidos.index') }}" method="GET" class="flex items-center space-x-2">
                @if(request('estado'))
                    <input type="hidden" name="estado" value="{{ request('estado') }}">
                @endif
                <div class="relative">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por ID, cliente o tel..." class="w-64 pl-9 pr-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    Buscar
                </button>
                @if(request('buscar') || request('estado'))
                    <a href="{{ route('pedidos.index') }}" class="text-xs text-slate-500 hover:text-slate-800 px-2 py-1.5">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla Principal de Pedidos -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4"># Pedido</th>
                        <th class="py-3.5 px-4">Cliente & Contacto</th>
                        <th class="py-3.5 px-4">Dirección de Entrega</th>
                        <th class="py-3.5 px-4">Método de Pago</th>
                        <th class="py-3.5 px-4">Domiciliario</th>
                        <th class="py-3.5 px-4">Estado</th>
                        <th class="py-3.5 px-4 text-right">Total</th>
                        <th class="py-3.5 px-4">Fecha</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pedidos as $pedido)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <a href="{{ route('pedidos.show', $pedido) }}" class="text-orange-600 hover:underline">
                                    #{{ $pedido->id }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $pedido->cliente->nombre }}</div>
                                <div class="text-xs text-slate-500"><i class="fa-solid fa-phone text-slate-400 mr-1"></i>{{ $pedido->cliente->telefono }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600 max-w-xs truncate" title="{{ $pedido->direccion_entrega }}">
                                <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i>{{ $pedido->direccion_entrega }}
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium">
                                    {{ $pedido->metodoPago->nombre }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                @if($pedido->domiciliario)
                                    <div class="font-medium text-slate-800"><i class="fa-solid fa-motorcycle text-slate-400 mr-1"></i>{{ $pedido->domiciliario->nombre }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $pedido->domiciliario->telefono }}</div>
                                @else
                                    <span class="text-slate-400 italic">Sin asignar</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ $pedido->estado }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-800">
                                ${{ number_format($pedido->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $pedido->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5">
                                    <a href="{{ route('pedidos.show', $pedido) }}" class="p-1.5 text-slate-600 hover:text-orange-600 hover:bg-slate-100 rounded transition" title="Ver Comanda / Factura">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                    <a href="{{ route('pedidos.edit', $pedido) }}" class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition" title="Editar Pedido">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este pedido #{{ $pedido->id }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded transition" title="Eliminar Pedido">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300 block"></i>
                                No se encontraron pedidos con los criterios seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pedidos->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

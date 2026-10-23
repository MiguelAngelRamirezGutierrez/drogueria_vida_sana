@extends('layouts.app')

@section('title', 'Módulo de Ventas & Facturación')
@section('page_title', 'Registro y Control de Ventas')

@section('content')
<div class="space-y-6">

    <!-- Tarjetas de Resumen y Acciones -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-tr from-slate-900 to-slate-800 text-white p-6 rounded-2xl shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Total Facturado este Mes</p>
                <p class="text-2xl font-black mt-1 text-emerald-400">
                    ${{ number_format((float) $totalMonth, 2, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400 mt-1">IVA del 19% liquidado en transacciones</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-xl text-emerald-400">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between md:col-span-2">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Facturación y Descuento de Stock</h3>
                <p class="text-xs text-slate-500 mt-1">Al emitir una venta se reserva stock con transacciones atómicas (<code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700">DB::transaction</code>).</p>
            </div>
            @can('crear-ventas')
                <a href="{{ route('sales.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-md shadow-orange-500/20 transition whitespace-nowrap">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>Nueva Venta</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por N° factura o cliente..."
                       class="w-full pl-8 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
            </div>
            <div class="flex items-center space-x-2 text-xs text-slate-500">
                <span>Desde:</span>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
            </div>
            <div class="flex items-center space-x-2 text-xs text-slate-500">
                <span>Hasta:</span>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
            </div>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition">
                <i class="fa-solid fa-filter mr-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search', 'from', 'to']))
                <a href="{{ route('sales.index') }}" class="text-xs text-slate-500 hover:text-slate-700 underline py-2">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                <thead class="bg-slate-50/75 text-slate-600 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Factura</th>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Vendedor</th>
                        <th class="px-6 py-4 text-right">Subtotal</th>
                        <th class="px-6 py-4 text-right">IVA (19%)</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-normal">
                    @forelse ($sales as $sale)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-mono font-bold text-xs text-slate-900 whitespace-nowrap">
                                <span class="bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 rounded-md">
                                    {{ $sale->invoice_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs whitespace-nowrap">
                                {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900">
                                {{ $sale->client?->name ?? 'Cliente no disponible' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $sale->user?->name ?? 'Sistema' }}
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-mono">
                                ${{ number_format((float) $sale->subtotal, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-xs font-mono text-slate-500">
                                ${{ number_format((float) $sale->tax, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-sm text-slate-900">
                                ${{ number_format((float) $sale->total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sale->status === 'pagada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <i class="fa-solid fa-check mr-1 text-[10px]"></i> Pagada
                                    </span>
                                @elseif($sale->status === 'anulada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700">
                                        <i class="fa-solid fa-ban mr-1 text-[10px]"></i> Anulada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center space-x-1 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Ver Factura</span>
                                </a>
                                @can('eliminar-ventas')
                                    @if($sale->status !== 'anulada')
                                        <form method="POST" action="{{ route('sales.destroy', $sale) }}" class="inline"
                                              onsubmit="return confirm('¿Anular esta venta? El stock de los productos será devuelto inmediatamente.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center space-x-1 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">
                                                <i class="fa-solid fa-rotate-left"></i>
                                                <span>Anular</span>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-invoice text-3xl mb-2 block"></i>
                                No se encontraron ventas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
